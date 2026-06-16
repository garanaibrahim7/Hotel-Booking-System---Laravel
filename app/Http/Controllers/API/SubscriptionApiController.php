<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlans;
use App\Models\Subscriptions; // -> ADDED: Import the active subscriptions model
use App\Models\SubscriptionsHistory;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubscriptionApiController extends Controller
{
    protected SubscriptionService $subscriptionObj;

    // -> UPDATED: Use Dependency Injection
    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionObj = $subscriptionService;
    }

    public function index(Request $request)
    {
        $plans = SubscriptionPlans::all();
        $subscribed = Subscriptions::where('user_id', $request->user()->id)
            ->value('subscription_plan_id');
        // Log::channel('debug')->info('Subscribed Plan ID: '. $subscribed);

        return [
            'success' => true,
            'data' => $plans->map(function ($plan) use ($subscribed) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'description' => $plan->description,
                    'price' => (float) $plan->price,
                    'currency_symbol' => $plan->currency_symbol,
                    'billing_period' => match ($plan->type) {
                        'monthly' => 'month',
                        'quarterly' => 'quarter',
                        'yearly' => 'year',
                        default => $plan->type,
                    },
                    'tag' => match (true) {
                        str_contains(strtolower($plan->name), 'traveller') => 'popular',
                        // str_contains(strtolower($plan->name), 'plan') => 'popular',
                        // str_contains(strtolower($plan->name), 'ultra') => 'ultra',
                        default => 'basic',
                    },
                    'subscribed' => $plan->id === $subscribed,
                    'features' => $plan->facilities ?? [],
                ];
            })->values(),
        ];
    }

    public function planSummary(SubscriptionPlans $plan)
    {
        $taxPercent = 5;
        $tax = [
            'percent' => $taxPercent,
            'amount' => (float) ($plan->price / $taxPercent),
        ];

        try {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'price' => (float) $plan->price,
                    'pricing' => [
                        'amount' => (float) $plan->price,
                        'tax' => $tax,
                        'payable_amount' => $plan->price + $tax['amount'],
                    ],
                    'currency' => strtoupper($plan->currency),
                    'currency_symbol' => $plan->currency_symbol,
                    'type' => $plan->type,
                    'facilities' => is_array($plan->facilities)
                                            ? $plan->facilities
                                            : (json_decode($plan->facilities, true) ?? []),
                ],
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error: '.$e->getMessage(),
            ], 404);
        }
    }

    public function planSubscribe(Request $request)
    {
        $request->validate([
            'planId' => 'required',
        ]);

        $res = $this->subscriptionObj->processSubscription($request->planId);

        return response()->json($res);
    }

    public function subscriptionDetails(Request $request, $historyId)
    {
        // sleep(2);
        $user = $request->user();

        if ($historyId === 'current') {
            $sub = Subscriptions::where('user_id', $user->id)
                ->whereIn('status', ['active', 'cancelled'])
                ->first();
            Log::info('Sub: '.$sub);

            $history = SubscriptionsHistory::with('plan')
                ->where('stripe_price_id', $sub?->stripe_price_id)
                ->first();

        } else {
            $history = SubscriptionsHistory::with('plan')
                ->where('id', $historyId)
                ->where('user_id', $user->id)
                ->first();
        }
        if (! $history) {
            return response()->json([
                'success' => false,
                'message' => 'Subscription ledger record not found.',
            ], 404);
        }

        $activeSubscription = Subscriptions::where('user_id', $user->id)
            ->where('subscription_plan_id', $history->subscription_plan_id)
            ->first();

        $renewalOn = null;
        $displayStatus = 'pending';

        if ($history->status == 1) {
            $displayStatus = $activeSubscription ? $activeSubscription->status : 'processing';
            $renewalOn = $activeSubscription ? $activeSubscription->renewal_on : null;
        } elseif ($history->status == 2) {
            $displayStatus = 'failed';
        } elseif ($history->status == 4) {
            $displayStatus = 'cancelled';
        }

        Log::info('History : ', ['Plan' => $history->plan?->toArray()]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $history->id,
                'status' => $displayStatus,
                'renewal_on' => $renewalOn,
                'amount' => (float) $history->amount,
                'currency' => $history->currency,
                'created_at' => $history->created_at,
                'plan' => [
                    'id' => $history->plan?->id,
                    'name' => $history->plan?->name,
                    'price' => (float) $history->plan->price,
                    'currency_symbol' => $history->plan->currency_symbol,
                    'type' => $history->plan->type,
                    'facilities' => is_array($history->plan->facilities)
                                        ? $history->plan->facilities
                                        : (json_decode($history->plan->facilities, true) ?? []),
                ],
            ],
        ], 200);
    }

    // public function currentSubsctiption(Request $request)
    // {
    //     $curPlan = Subscriptions::with('plan')
    //         ->where('user_id', $request->user()->id)->first();

    // }

    public function currentPlan(Request $request)
    {
        $curPlan = Subscriptions::with('plan')
            ->where('user_id', $request->user()->id)
            ->whereIn('status', ['active', 'cancelled'])
            ->first();

        if (! $curPlan || ! $curPlan->plan) {
            return response()->json([
                'success' => false,
                'message' => 'No active subscription found.',
                'plan_name' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'plan_id' => $curPlan->plan->id,
            'plan_name' => $curPlan->plan->name,
        ], 200);
    }

    public function cancel(Request $request, SubscriptionService $subscriptionService)
    {
        // sleep(5);
        // return response()->json([
        //         'status' => 'success',
        //         'message' => 'Test message',
        //         'access_until' => 'access_until'
        //     ], 200);
        $user = $request->user();

        $result = $subscriptionService->cancelSubscription($user->id);

        if ($result['success']) {
            return response()->json([
                'status' => 'success',
                'message' => $result['message'],
                'access_until' => $result['access_until'],
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => $result['message'],
        ], 400);
    }

    public function updatePaymentMethod(Request $request)
    {
        $res = $this->subscriptionObj->updatePaymentMethod($request->user()->id);
        if ($res['success']) {
            return response()->json($res, 200);
        }

        return response()->json($res, 500);
    }

    public function manageSubscription(Request $request)
    {
        $res = $this->subscriptionObj->manageSubscription($request->user()->id);
        if ($res['success']) {
            return response()->json($res, 200);
        }

        return response()->json($res, 500);
    }

    public function planChangeSummary(Request $request)
    {
        $request->validate([
            'newPlanId' => 'required|exists:subscription_plans,id',
        ]);

        $user = $request->user();

        // 1. Get current active subscription with its plan
        $currentSub = Subscriptions::with('plan')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (! $currentSub || ! $currentSub->plan) {
            return response()->json([
                'success' => false,
                'message' => 'No active subscription found to change.',
            ], 404);
        }

        $currentPlan = $currentSub->plan;
        $newPlan = SubscriptionPlans::findOrFail($request->newPlanId);

        // Prevent swapping to the exact same plan
        if ($currentPlan->id === $newPlan->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are already subscribed to this plan.',
            ], 400);
        }

        // 2. Determine if it is an Upgrade or Downgrade based on price
        $isUpgrade = $newPlan->price > $currentPlan->price;

        $summary = [
            'change_type' => $isUpgrade ? 'upgrade' : 'downgrade',
            'current_plan' => [
                'name' => $currentPlan->name,
                'price' => (float) $currentPlan->price,
                'currency_symbol' => $currentPlan->currency_symbol,
            ],
            'new_plan' => [
                'name' => $newPlan->name,
                'price' => (float) $newPlan->price,
                'currency_symbol' => $newPlan->currency_symbol,
            ],
        ];

        // 3. Format the response payload based on the change type
        if (! $isUpgrade) {
            $summary['action_text'] = 'Confirm Downgrade';
            $summary['details'] = 'Your plan will be downgraded at the end of your current billing cycle. You will not be charged today.';
            $summary['charge_today'] = 0;
            $summary['effective_date'] = $currentSub->renewal_on;
        } else {
            $summary['action_text'] = 'Confirm Upgrade';
            $summary['details'] = 'Your plan will be upgraded immediately. A prorated charge will be applied to your default payment method.';

            // Note: To get the EXACT prorated dollar amount, we will need to query Stripe's Upcoming Invoice API.
            // For now, we indicate a prorated charge will occur.
            $summary['charge_today'] = 'prorated';
            $summary['effective_date'] = Carbon::now()->toDateTimeString();
        }

        return response()->json([
            'success' => true,
            'data' => $summary,
        ], 200);
    }

    public function executePlanChange(Request $request)
    {
        $request->validate([
            'newPlanId' => 'required|exists:subscription_plans,id',
        ]);

        $user = $request->user();

        $currentSub = Subscriptions::with('plan')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (! $currentSub || ! $currentSub->plan) {
            return response()->json([
                'success' => false,
                'message' => 'No active subscription found to change.',
            ], 400);
        }

        $currentPlan = $currentSub->plan;
        $newPlan = SubscriptionPlans::findOrFail($request->newPlanId);

        if ($currentPlan->id === $newPlan->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are already subscribed to this plan.',
            ], 400);
        }

        // Re-verify the change type securely on the server
        $isUpgrade = $newPlan->price > $currentPlan->price;

        if (! $isUpgrade) {
            // Route to the Downgrade logic we just built
            $res = $this->subscriptionObj->downgradePlan($user->id, $newPlan->id);
        } else {
            // Route to the Upgrade logic (We will build this next)
            // $res = $this->subscriptionObj->upgradePlan($user->id, $newPlan->id);

            // Placeholder until we write the upgrade method
            $res = [
                'success' => false,
                'message' => 'Upgrade logic is pending implementation.',
            ];
        }

        if (isset($res['success']) && $res['success']) {
            return response()->json([
                'success' => true,
                'message' => $res['message'],
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => $res['message'] ?? 'Failed to process subscription change. Please try again.',
        ], 500);
    }
}
