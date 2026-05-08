<?php

namespace App\Contracts;

use App\Models\Booking;

interface PaymentProviderInterface
{
    public function createPaymentSession($bookingDetails): array;

    public function refund(float $amount,
        string $payment_intent_id,
        ?string $reason = null);

    public function expireSession(string $sessionId): bool;

    public function getProviderName(): string;
}
