<?php

namespace App\Services\Payments;

use App\Contracts\PaymentProviderInterface;
use Exception;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class StripeProvider implements PaymentProviderInterface
{
    protected StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    public function activeSessions()
    {
        return $this->stripe->checkout->sessions->all();
    }

    public function createPaymentSession($bookingDetails): array
    {
        try {
            $lineItems = array_map(function ($item) use ($bookingDetails) {
                return [
                    'price_data' => [
                        'currency' => $bookingDetails['currency'],
                        'product_data' => [
                            'name' => $item['room_title'],
                            'description' => 'Room Number: '.$item['room_number'],
                            'images' => [$item['image']],
                        ],

                        'unit_amount' => round($item['price'] * 100),
                    ],
                    'quantity' => $item['nights'],
                ];
            }, $bookingDetails['items']);

            $sessionPayload = [
                'line_items' => $lineItems,
                'mode' => 'payment',
                'customer_email' => $bookingDetails['customer_email'],
                'client_reference_id' => $bookingDetails['client_reference_id'],
                'metadata' => [
                    ...$bookingDetails['metadata'],
                    'booking_id' => $bookingDetails['booking_id'],
                ],
                
                'success_url' => $bookingDetails['success_url'].'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $bookingDetails['cancel_url'].'?session_id={CHECKOUT_SESSION_ID}',
                'expires_at' => time() + (30 * 60),
            ];

            if ($bookingDetails['discount_amount'] > 0) {
                $stripeCoupon = $this->stripe->coupons->create([
                    'amount_off' => round($bookingDetails['discount_amount'] * 100),
                    'currency' => $bookingDetails['currency'],
                    'duration' => 'once',
                    'name' => 'System Discount',
                ]);

                $sessionPayload['discounts'] = [
                    ['coupon' => $stripeCoupon->id],
                ];
            }

            $checkout_session = $this->stripe->checkout->sessions->create($sessionPayload);

            Log::channel('debug')->info('Check out Session : ', $checkout_session->toArray());

            return [
                'url' => $checkout_session->url,
                'session_id' => $checkout_session->id,
            ];
        } catch (Exception $e) {
            Log::channel('debug')->error('StripeProvider Error : '.$e->getMessage());

            return [
                'error' => 'Something went Wrong',
            ];
        }
    }

    public function refund(float $amount, $payment_intent_id, $reason = null)
    {

        Log::channel('debug')->critical('Refund Amount : '.$amount . ' - payment intent'. $payment_intent_id );
        try {
            $refundInstance = $this->stripe->refunds->create([
                'amount' => $amount * 100,
                'payment_intent' => $payment_intent_id,
                'reason' => 'requested_by_customer',
            ]);

            return [
                'success' => true,
                'message' => 'Refund Initialized Successfully',
                'data' => [
                    'refund_id' => $refundInstance->id,
                    'amount' => $amount,
                    'currency' => $refundInstance->currency,
                    'status' => $refundInstance->status,
                ],
            ];
        } catch (Exception $e) {
            Log::channel('failures')->critical('Refund Initialize Exception : '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Refund not Initialize',
                // 'data' => [
                //     'refund_id' => $refundInstance->id,
                //     'amount' => $amount,
                //     'currency' => $refundInstance->currency,
                // ],
            ];
        }
    }

    public function refund_test(float $amount, $payment_intent_id, $payment_id, $booking_id, $paid_currency, $user_id)
    {
        return $this->stripe->refunds->all(['payment_intent' => $payment_intent_id]);

        return $this->stripe->refunds->create([
            'amount' => $amount * 100,
            'payment_intent' => $payment_intent_id,
            'reason' => 'requested_by_customer',
            // "customer"=> 'Test Refund Customer',
            // "instructions_email"=> auth()->user()->email,
            // "refund_application_fee"=> 0,
            // "charge"=> null,
            // "expand"=> null,
            // "metadata"=> null,
            // "origin"=> null,
            // "reverse_transfer"=> null,

        ]);

        // "amount": int,
        return true;
    }

    public function expireSession(string $sessionId): bool
    {
        $this->stripe->checkout->sessions->expire($sessionId, []);

        return true;
    }

    public function getProviderName(): string
    {
        return 'Stripe';
    }
}
