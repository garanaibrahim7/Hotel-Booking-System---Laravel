<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    const STATUS_PENDING = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_FAILED = 2;
    const STATUS_PROCESSING = 3;
    const STATUS_CANCELLED = 4;
    const STATUS_REFUNDED = 6;

    protected $fillable = [
        'booking_id',
        'session_id',
        'payment_intent_id',
        'amount',
        'currency',
        'gateway',
        'status',
        'converted_amount',
        'paid_currency',
        'exchange_rate',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
