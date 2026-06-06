<?php

namespace App\Services;

use App\Contracts\SubscriptionProviderInterface;
use App\Events\BroadcastSubscriptionStatus;
use App\Jobs\CreateTransactionJob;
use App\Models\SubscriptionPlans;
use App\Models\Subscriptions;
use App\Models\SubscriptionsHistory;
use App\Models\User;
use App\Notifications\SubscriptionConfirmNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SubscriptionService
{
    protected SubscriptionProviderInterface $subscriptionProvider;

    public function __construct(SubscriptionProviderInterface $provider)
    {
        $this->subscriptionProvider = $provider;
    }

    public function processSubscription($planId, $userCurrency = 'usd')
    {
        $user = Auth::user();
        $plan = SubscriptionPlans::findOrFail($planId);

        $isReactApp = (request()->wantsJson() || request()->is('api/*'));
        // $successUrl = $isReactApp ? env('FRONTEND_URL', 'http://localhost:5173').'/subscribe/success' : route('booking.success');
        // $cancelUrl = $isReactApp ? env('FRONTEND_URL', 'http://localhost:5173').'/subscribe/cancel' : route('booking.cancelPayment');
        $successUrl = $isReactApp ? env('FRONTEND_URL', 'http://localhost:5173').'/subscription/status' : route('booking.success');
        $cancelUrl = $isReactApp ? env('FRONTEND_URL', 'http://localhost:5173').'/subscription/status' : route('booking.cancelPayment');

        $subscriptionDetails = [
            'stripe_price_id' => $plan->stripe_price_id,
            'customer_email' => $user->email,
            'client_reference_id' => (string) $user->id,
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'currency' => $plan->currency ?? $userCurrency,
            'tax_percent' => 5,
            'metadata' => [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
            ],
        ];

        $checkoutSession = $this->subscriptionProvider->processSubscribePlan($subscriptionDetails);

        if (isset($checkoutSession['error'])) {
            return [
                'success' => false,
                'error' => $checkoutSession['error'],
            ];
        }

        $history = SubscriptionsHistory::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'stripe_session_id' => $checkoutSession['session_id'],
            'stripe_price_id' => $plan->stripe_price_id, // ADDED
            'amount' => $plan->price,
            'currency' => $plan->currency,
            'converted_amount' => $plan->price,
            'converted_currency' => $plan->currency,
            'status' => SubscriptionsHistory::STATUS_PENDING,
        ]);

        return [
            'success' => true,
            'stripe_id' => $checkoutSession['session_id'],
            'payment_url' => $checkoutSession['url'],
            'plan_name' => $plan->name,
            'amount' => $plan->price,
            'history_id' => $history->id,
            'currency' => $plan->currency,
        ];
    }

    public function cancelSubscription($userId)
    {
        $subscription = Subscriptions::where('user_id', $userId)
            ->whereIn('status', ['active', 'past_due'])
            ->first();

        if (! $subscription) {
            return [
                'success' => false,
                'message' => 'No active subscription found to cancel.',
            ];
        }

        $res = $this->subscriptionProvider->cancelSubscription($subscription->stripe_id);
        if ($res) {
            $subscription->update(['status', 'cancelled']);

            return [
                'success' => true,
                'message' => 'Your subscription has been canceled. You will not be billed again.',
                'access_until' => $subscription->renewal_on,
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to connect to the payment gateway. Please try again later.',
        ];

    }

    public function updatePaymentMethod($userId)
    {
        $isReactApp = (request()->wantsJson() || request()->is('api/*'));
        $subscription = Subscriptions::where('user_id', $userId)
            ->whereIn('status', ['active', 'cancelled'])
            ->first();

        return $this->subscriptionProvider->updatePaymentMethod([
            'stripe_customer_id' => $subscription->stripe_customer_id,
            'return_url' => $isReactApp ? env('FRONTEND_URL', 'http://localhost:5173').'/profile' : route('client.profile'),
        ]);
    }

    public function manageSubscription($userId)
    {
        $isReactApp = (request()->wantsJson() || request()->is('api/*'));
        $subscription = Subscriptions::where('user_id', $userId)
            ->whereIn('status', ['active', 'cancelled'])
            ->first();

        return $this->subscriptionProvider->manageSubscription([
            'stripe_customer_id' => $subscription->stripe_customer_id,
            'return_url' => $isReactApp ? env('FRONTEND_URL', 'http://localhost:5173').'/profile' : route('client.profile'),
        ]);
    }

    public function downgradePlan($userId, $newPlanId)
    {
        $localSub = Subscriptions::where('user_id', $userId)
            ->where('status', 'active')
            ->first();

        $newPlan = SubscriptionPlans::find($newPlanId);

        if (!$localSub || !$newPlan) {
            return [
                'success' => false,
                'message' => 'Invalid subscription or plan.'
            ];
        }

        $res = $this->subscriptionProvider->scheduleDowngrade([
            'stripe_subscription_id' => $localSub->stripe_id,
            'new_stripe_price_id' => $newPlan->stripe_price_id
        ]);

        if ($res['success']) {
            $localSub->update([
                'downgrades_to_plan_id' => $newPlanId
            ]);

            return [
                'success' => true,
                'message' => 'Plan downgraded successfully. You will keep your current premium features until ' . Carbon::createFromTimestamp($res['effective_date'])->toFormattedDateString() . '.',
            ];
        }

        return [
            'success' => false,
            'message' => $res['message']
        ];
    }

    public function createSubscriptionPlan($data)
    {
        return $this->subscriptionProvider->createPlan($data);
    }

    public function deleteSubscriptionPlan($productId)
    {
        return $this->subscriptionProvider->deletePlan($productId);
    }

    public function updateSubscriptionPlan($productId, $data)
    {
        return $this->subscriptionProvider->updatePlan($productId, $data);
    }

    public function finalizeSubscription($session, int $status)
    {
        Log::channel('debug')->info('Subscription service triggered by Webhook', ['status' => $status]);

        $sessionId = $session->id ?? null;
        $stripeSubscriptionId = $session->subscription ?? null;
        $stripeInvoiceId = $session->invoice ?? null;
        $stripeCustomerId = $session->customer ?? null;

        // ONLY attempt to find the Checkout History for checkout-based events (Statuses 1, 2, 3, 4)
        $history = null;
        if (in_array($status, [1, 2, 3, 4])) {
            $history = SubscriptionsHistory::where('stripe_session_id', $sessionId)->first();

            if (! $history) {
                Log::channel('debug')->error('Subscription history not found for session: '.$sessionId);

                return false;
            }
        }

        $userId = $history->user_id ?? null;
        $planId = $history->subscription_plan_id ?? null;

        if ($userId) {
            $user = User::find($userId);
        }

        switch ($status) {
            case 1:
                // Initial Checkout Success (No changes, working perfectly)
                $history->update([
                    'stripe_invoice_id' => $stripeInvoiceId,
                    'status' => SubscriptionsHistory::STATUS_SUCCESS,
                ]);

                $plan = SubscriptionPlans::find($planId);
                $renewalDate = match (strtolower($plan->type)) {
                    'monthly' => Carbon::now()->addMonth(),
                    '3 months' => Carbon::now()->addMonths(3),
                    '6 months' => Carbon::now()->addMonths(6),
                    'yearly' => Carbon::now()->addYear(),
                    'lifetime' => Carbon::now()->addYears(100),
                    default => Carbon::now()->addMonth(),
                };

                // NOTE: Create subscription record ONCE per user, then update
                $subscription = Subscriptions::updateOrCreate(
                    ['user_id' => $userId],
                    [
                        'subscription_plan_id' => $planId,
                        'stripe_id' => $stripeSubscriptionId,
                        'stripe_price_id' => $plan->stripe_price_id,
                        'stripe_customer_id' => $stripeCustomerId,
                        'status' => 'active',
                        'renewal_on' => $renewalDate,
                    ]
                );

                CreateTransactionJob::dispatch([
                    'transactionable_id' => $history->id,
                    'transactionable_type' => SubscriptionsHistory::class,
                    'amount' => (float) $history->amount,
                    'converted_amount' => (float) $history->converted_amount,
                    'currency' => $history->currency,
                    'converted_currency' => $history->converted_currency,
                    'exchange_rate' => (float) 1.0,
                    'type' => 'credit',
                    'mode' => 'stripe',
                    'note' => 'Subscribed By '.$history->user->name.' for Plan - '.$plan->name,
                    'tax' => 0,
                    'tax_amount' => 0,
                ]);

                event(new BroadcastSubscriptionStatus($history->id, true));

                if (isset($user)) {
                    $user->notify(new SubscriptionConfirmNotification($user, $plan, $subscription, $history));
                }
                break;

            case 5: // charge.dispute.created
            case 6: // charge.refunded
                // NOTE: Case 1 (Dispute/Refund) - Update sub status, Update history status, Entry in transaction for debit
                $invoiceId = $session->subscription_invoice_id ?? ($session->invoice ?? null);

                if ($invoiceId) {
                    $historyRecord = SubscriptionsHistory::where('stripe_invoice_id', $invoiceId)->first();

                    if ($historyRecord) {
                        $historyRecord->update([
                            'status' => $status == 5 ? 2 : 4,
                        ]);

                        // Debit Transaction
                        CreateTransactionJob::dispatch([
                            'transactionable_id' => $historyRecord->id,
                            'transactionable_type' => SubscriptionsHistory::class,
                            'amount' => (float) $historyRecord->amount,
                            'converted_amount' => (float) $historyRecord->converted_amount,
                            'currency' => $historyRecord->currency,
                            'converted_currency' => $historyRecord->converted_currency,
                            'exchange_rate' => (float) 1.0,
                            'type' => 'debit', // DEBIT ENTRY
                            'mode' => 'stripe',
                            'note' => ($status == 5 ? 'Dispute Debit' : 'Refund Debit').' for Plan - '.($historyRecord->plan->name ?? ''),
                            'tax' => 0,
                            'tax_amount' => 0,
                        ]);

                        // Update subscription status sent by stripe
                        $sub = Subscriptions::where('user_id', $historyRecord->user_id)->first();
                        if ($sub) {
                            $sub->update(['status' => $status == 5 ? 'disputed' : 'refunded']);
                        }
                    }
                }
                break;

            case 7:
                $stripeSubId = $session->id;
                $newStatus = $session->status ?? 'canceled'; // Stripe will send 'canceled'

                Subscriptions::where('stripe_id', $stripeSubId)->update([
                    'status' => $newStatus,
                ]);
                break;

            case 8:
                $stripeSubId = $session->subscription;
                $subscription = Subscriptions::where('stripe_id', $stripeSubId)->first();

                if ($subscription) {
                    $plan = SubscriptionPlans::find($subscription->subscription_plan_id);
                    $renewalDate = match (strtolower($plan->type)) {
                        'monthly' => Carbon::now()->addMonth(),
                        '3 months' => Carbon::now()->addMonths(3),
                        '6 months' => Carbon::now()->addMonths(6),
                        'yearly' => Carbon::now()->addYear(),
                        'lifetime' => Carbon::now()->addYears(100),
                        default => Carbon::now()->addMonth(),
                    };

                    $subscription->update([
                        'status' => 'active',
                        'renewal_on' => $renewalDate,
                    ]);

                    $amountPaid = isset($session->amount_paid) ? ($session->amount_paid / 100) : $plan->price;
                    $currency = $session->currency ?? $plan->currency;

                    $renewalHistory = SubscriptionsHistory::create([
                        'user_id' => $subscription->user_id,
                        'subscription_plan_id' => $plan->id,
                        'stripe_session_id' => null,
                        'stripe_invoice_id' => $session->id,
                        'stripe_price_id' => $plan->stripe_price_id,
                        'amount' => $amountPaid,
                        'currency' => $currency,
                        'converted_amount' => $amountPaid,
                        'converted_currency' => $currency,
                        'status' => SubscriptionsHistory::STATUS_SUCCESS,
                    ]);

                    CreateTransactionJob::dispatch([
                        'transactionable_id' => $renewalHistory->id,
                        'transactionable_type' => SubscriptionsHistory::class,
                        'amount' => (float) $renewalHistory->amount,
                        'converted_amount' => (float) $renewalHistory->converted_amount,
                        'currency' => $renewalHistory->currency,
                        'converted_currency' => $renewalHistory->converted_currency,
                        'exchange_rate' => (float) 1.0,
                        'type' => 'credit',
                        'mode' => 'stripe',
                        'note' => 'Subscription Auto-Renewal for Plan - '.$plan->name,
                        'tax' => 0,
                        'tax_amount' => 0,
                    ]);
                }
                break;

            case 9:
                $stripeSubId = $session->subscription;
                $subscription = Subscriptions::where('stripe_id', $stripeSubId)->first();

                if ($subscription) {
                    $subscription->update([
                        'status' => 'past_due',
                    ]);

                    $historyRecord = SubscriptionsHistory::where('stripe_invoice_id', $session->id)->first();
                    if ($historyRecord) {
                        $historyRecord->update(['status' => 2]);
                    }
                }
                break;

            case 10:
                $stripeSubId = $session->id;
                if ($session->cancel_at_period_end === true) {
                    $newStatus = 'cancelled';
                } else {
                    $newStatus = $session->status ?? 'active';
                }

                // Log::channel('debug')->info('Session : ', $session->toArray());
                // Log::channel('debug')->info('in Update Case, Updated Status: '.$session->status);
                Subscriptions::where('stripe_id', $stripeSubId)->update([
                    'status' => $newStatus,
                ]);
                break;

            default:
                Log::channel('debug')->warning('Unhandled Subscription Status: '.$status);
                break;
        }

        return true;
    }
}
