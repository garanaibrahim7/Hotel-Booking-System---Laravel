<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscriptions extends Model
{
    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'stripe_id',
        'stripe_price_id',
        'stripe_customer_id',
        'status',
        'renewal_on',
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
