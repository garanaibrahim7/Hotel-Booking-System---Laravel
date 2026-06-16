<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refund extends Model
{
    const STATUS_PENDING = 0;
    const STATUS_REFUNDED = 1;
    const STATUS_FAILED = 2;
    const STATUS_PROCESSING = 3;
    const STATUS_REJECTED = 4;
    protected $fillable = [
        'refund_id',
        'amount',
        'currency',
        'status',
        'user_id',
        'payment_id',
        'booking_id',
        'reason',
        'note',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
