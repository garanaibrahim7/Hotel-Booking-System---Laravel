<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlans extends Model
{
    protected $fillable = [
        'name',
        'price',
        'currency',
        'currency_symbol',
        'stripe_price_id',
        'stripe_product_id',
        'type',
        'facilities',
    ];

    protected function casts(): array
    {
        return [
            'facilities' => 'array',
        ];
    }
}
