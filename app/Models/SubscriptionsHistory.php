<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionsHistory extends Model
{
    const STATUS_PENDING = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_FAILED = 2;
    const STATUS_PROCESSING = 3;
    const STATUS_CANCELLED = 4;
    const STATUS_REJECTED = 5;
    const STATUS_REFUNDED = 6;
    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'amount',
        'currency',
        'converted_amount',
        'converted_currency',
        'stripe_invoice_id',
        'stripe_session_id',
        'stripe_price_id',
        'status',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlans::class, 'subscription_plan_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
