<?php

namespace App\Services\Payments;

use App\Contracts\PaymentProviderInterface;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;

class RazorPayProvider implements PaymentProviderInterface
{
    public function createPaymentSession($bookingDetails): array
    {
        try {
            $api = new Api(
                config('services.razorpay.key'),
                config('services.razorpay.secret')
            );

            // ✅ Calculate total (same as you did)
            $amount = 0;

            foreach ($bookingDetails['items'] as $item) {
                $amount += $item['price'] * $item['nights'];
            }

            $amount -= $bookingDetails['discount_amount'];

            // Optional: build description from items
            $description = collect($bookingDetails['items'])
                ->map(fn($item) => $item['room_title'] . ' (' . $item['nights'] . 'N)')
                ->implode(', ');

            // ✅ Create Payment Link (HOSTED PAGE)
            $paymentLink = $api->paymentLink->create([
                'amount' => round($amount * 100), // paise
                'currency' => 'INR',

                'description' => $description,

                'customer' => [
                    'name'  => $bookingDetails['customer_name'] ?? 'Guest',
                    'email' => $bookingDetails['customer_email'],
                ],

                'notify' => [
                    'email' => true,
                ],

                'callback_url' => $bookingDetails['success_url'],
                'callback_method' => 'get',

                'notes' => [
                    'booking_id' => $bookingDetails['booking_id'],
                    'reference'  => $bookingDetails['client_reference_id'],
                ]
            ]);

            return [
                'url' => $paymentLink['short_url'],
                'id'  => $paymentLink['id'],
            ];
        } catch (\Exception $e) {
            Log::error('Razorpay Error: ' . $e->getMessage());
            return ['error' => 'Something went wrong'];
        }
    }

    public function refund(string $transactionId, float $amount): bool
    {
        return false;
    }

    public function expireSession(string $sessionId): bool
    {
        return false;
    }

    public function getProviderName(): string
    {
        return '';
    }
}
