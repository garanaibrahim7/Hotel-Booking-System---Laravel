<?php

namespace App\Services\Subscription;

use App\Contracts\SubscriptionProviderInterface;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Stripe\StripeClient;

class StripeSubscriptionProvider implements SubscriptionProviderInterface
{
    protected StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    public function processSubscribePlan($payload): array
    {
        // Log::channel('debug')->info('Arrived in Stripe Subscription Provider');

        try {
            $sessionPayload = [
                'line_items' => [
                    [
                        'price' => $payload['stripe_price_id'],
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'subscription',
                'customer_email' => $payload['customer_email'],
                'client_reference_id' => $payload['client_reference_id'],
                'metadata' => [
                    ...$payload['metadata'],
                    // 'plan_id' => $payload['plan_id'],
                ],

                'success_url' => $payload['success_url'],
                'cancel_url' => $payload['cancel_url'],
                // 'success_url' => $payload['success_url'].'?session_id={CHECKOUT_SESSION_ID}',
                // 'cancel_url' => $payload['cancel_url'].'?session_id={CHECKOUT_SESSION_ID}',
                'expires_at' => time() + (30 * 60),
            ];

            if (isset($payload['tax_percent']) && $payload['tax_percent'] > 0) {
                $taxRate = $this->stripe->taxRates->create([
                    'display_name' => 'Tax',
                    'description' => 'Platform Tax',
                    'percentage' => $payload['tax_percent'],
                    'inclusive' => false,
                ]);
                $sessionPayload['line_items'][0]['tax_rates'] = [$taxRate->id];
            }

            // if (isset($payload['discount_amount']) && $payload['discount_amount'] > 0) {
            //     $stripeCoupon = $this->stripe->coupons->create([
            //         'amount_off' => round($payload['discount_amount'] * 100),
            //         'currency' => $payload['currency'],
            //         'duration' => 'once',
            //         'name' => 'System Discount',
            //     ]);

            //     $sessionPayload['discounts'] = [
            //         ['coupon' => $stripeCoupon->id],
            //     ];
            // }

            $checkout_session = $this->stripe->checkout->sessions->create($sessionPayload);

            Log::channel('debug')->info('Subscription Checkout Session : ', $checkout_session->toArray());

            return [
                'url' => $checkout_session->url,
                'session_id' => $checkout_session->id,
            ];
        } catch (Exception $e) {
            Log::channel('debug')->error('StripeProvider Subscription Error : '.$e->getMessage());

            return [
                'error' => 'Something went Wrong',
            ];
        }
    }

    public function cancelSubscription(string $stripe_id): bool
    {
        try {

            $this->stripe->subscriptions->update(
                $stripe_id,
                ['cancel_at_period_end' => true]
            );

            return true;

        } catch (Exception $e) {
            Log::channel('debug')->error('Cancel Subscription API Error: '.$e->getMessage());

            return false;
        }
    }

    public function updatePaymentMethod(array $payload): array
    {
        try {

            $session = $this->stripe->billingPortal->sessions->create([
                'customer' => $payload['stripe_customer_id'],
                'return_url' => $payload['return_url'],
                'flow_data' => [
                    'type' => 'payment_method_update',
                ],
            ]);

            return [
                'success' => true,
                'url' => $session->url,
            ];
        } catch (Exception $e) {
            Log::channel('debug')->error('Update Payment Method API Error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function manageSubscription(array $payload): array
    {
        try {

            $session = $this->stripe->billingPortal->sessions->create([
                'customer' => $payload['stripe_customer_id'],
                'return_url' => $payload['return_url'],
                // 'flow_data' => [
                //     'type' => 'payment_method_update',
                // ],
            ]);

            return [
                'success' => true,
                'url' => $session->url,
            ];
        } catch (Exception $e) {
            Log::channel('debug')->error('Manage Subscription API Error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function scheduleDowngrade(array $payload): array
    {
        try {
            $stripeSub = $this->stripe->subscriptions->retrieve($payload['stripe_subscription_id']);
            $currentPriceId = $stripeSub->items->data[0]->price->id;

            $schedule = $this->stripe->subscriptionSchedules->create([
                'from_subscription' => $payload['stripe_subscription_id'],
            ]);

            $this->stripe->subscriptionSchedules->update(
                $schedule->id,
                [
                    'end_behavior' => 'release',
                    'phases' => [
                        [
                            'start_date' => $schedule->phases[0]->start_date,
                            'end_date' => $stripeSub->current_period_end,
                            'items' => [
                                ['price' => $currentPriceId, 'quantity' => 1],
                            ],
                        ],
                        [
                            'start_date' => $stripeSub->current_period_end,
                            'items' => [
                                ['price' => $payload['new_stripe_price_id'], 'quantity' => 1],
                            ],
                        ],
                    ],
                ]
            );

            return [
                'success' => true,
                'effective_date' => $stripeSub->current_period_end
            ];

        } catch (Exception $e) {
            Log::channel('debug')->error('Stripe Schedule Downgrade Error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to schedule downgrade with payment gateway.',
            ];
        }
    }

    public function createPlan($payload): array
    {
        $validator = Validator::make($payload, [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|min:3|max:3',
            'type' => 'required|in:monthly,3 months,6 months,yearly,lifetime',
            'facilities' => 'nullable|array',
            'facilities.*' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $marketingFeatures = [];
        if (! empty($payload['facilities'])) {
            foreach ($payload['facilities'] as $facility) {
                $marketingFeatures[] = [
                    'name' => $facility,
                ];
            }
        }

        $interval = 'month';
        $interval_count = 1;

        if ($payload['type'] === '3 months') {
            $interval_count = 3;
        } elseif ($payload['type'] === '6 months') {
            $interval_count = 6;
        } elseif ($payload['type'] === 'yearly') {
            $interval = 'year';
        }

        $priceData = [
            'unit_amount' => (float) floor($payload['price'] * 100),
            'currency' => strtolower($payload['currency']),
        ];

        if ($payload['type'] !== 'lifetime') {
            $priceData['recurring'] = [
                'interval' => $interval,
                'interval_count' => $interval_count,
            ];
        }

        $productPayload = [
            'name' => $payload['name'],
            'description' => 'Premium tier membership access pass: '.$payload['name'],
            'default_price_data' => $priceData,
        ];

        if (! empty($marketingFeatures)) {
            $productPayload['marketing_features'] = $marketingFeatures;
        }

        $stripeProduct = $this->stripe->products->create($productPayload);

        return [
            'stripe_product_id' => $stripeProduct->id,
            'stripe_price_id' => $stripeProduct->default_price,
        ];
    }

    public function updatePlan(string $stripeProductId, array $payload): array
    {
        $validator = Validator::make($payload, [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|min:3|max:3',
            'type' => 'required|in:monthly,3 months,6 months,yearly,lifetime',
            'facilities' => 'nullable|array',
            'facilities.*' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $marketingFeatures = [];
        if (! empty($payload['facilities'])) {
            foreach ($payload['facilities'] as $facility) {
                $marketingFeatures[] = [
                    'name' => $facility,
                ];
            }
        }

        $product = $this->stripe->products->retrieve($stripeProductId);

        $currentPriceId = $product->default_price;
        $needsNewPrice = true;

        if ($currentPriceId) {
            $currentPrice = $this->stripe->prices->retrieve($currentPriceId);

            $targetAmount = (float) floor($payload['price'] * 100);
            $targetCurrency = strtolower($payload['currency']);

            $expectedInterval = 'month';
            $expectedIntervalCount = 1;

            if ($payload['type'] === '3 months') {
                $expectedIntervalCount = 3;
            } elseif ($payload['type'] === '6 months') {
                $expectedIntervalCount = 6;
            } elseif ($payload['type'] === 'yearly') {
                $expectedInterval = 'year';
            }

            $amountMatches = ($currentPrice->unit_amount === $targetAmount);
            $currencyMatches = ($currentPrice->currency === $targetCurrency);

            if ($payload['type'] === 'lifetime') {
                $typeMatches = ($currentPrice->type === 'one_time');
                $recurringMatches = true;
            } else {
                $typeMatches = ($currentPrice->type === 'recurring');
                $recurringMatches = isset($currentPrice->recurring) &&
                                    ($currentPrice->recurring->interval === $expectedInterval) &&
                                    ($currentPrice->recurring->interval_count === $expectedIntervalCount);
            }

            if ($amountMatches && $currencyMatches && $typeMatches && $recurringMatches) {
                $needsNewPrice = false;
            }
        }

        $productUpdatePayload = [
            'name' => $payload['name'],
            'description' => 'Premium tier membership access pass: '.$payload['name'],
            'marketing_features' => ! empty($marketingFeatures) ? $marketingFeatures : [],
        ];

        if ($needsNewPrice) {
            // 1. Prepare and generate the NEW price configuration payload first
            $pricePayload = [
                'product' => $stripeProductId,
                'unit_amount' => (float) floor($payload['price'] * 100),
                'currency' => strtolower($payload['currency']),
            ];

            if ($payload['type'] !== 'lifetime') {
                $pricePayload['recurring'] = [
                    'interval' => $expectedInterval,
                    'interval_count' => $expectedIntervalCount,
                ];
            }

            $newPrice = $this->stripe->prices->create($pricePayload);

            // 2. Set the newly generated price ID as the default for the product modification payload
            $productUpdatePayload['default_price'] = $newPrice->id;

            // 3. Update the product immediately so Stripe officially switches the default price reference!
            $this->stripe->products->update($stripeProductId, $productUpdatePayload);

            // 4. NOW it is safe to deactivate the old price because it is no longer the default on Stripe's servers
            if ($currentPriceId) {
                $this->stripe->prices->update($currentPriceId, [
                    'active' => false,
                ]);
            }

            // Sync the local tracker variable to point to the new pricing element
            $currentPriceId = $newPrice->id;
        } else {
            // 5. If we don't need a new price, just update the cosmetic and facility details normally
            $this->stripe->products->update($stripeProductId, $productUpdatePayload);
        }

        return [
            'stripe_product_id' => $stripeProductId,
            'stripe_price_id' => $currentPriceId,
        ];
    }

    public function deletePlan(string $productId): array
    {
        try {
            $this->stripe->products->update($productId, [
                'active' => false,
            ]);

            return [
                'success' => true,
                'message' => 'Plan Deleted from Stripe',
            ];
        } catch (Exception $ex) {
            return [
                'success' => false,
                'message' => 'Stripe Error to Delete Plan',
                'exception' => $ex,
            ];
        }
    }
}
