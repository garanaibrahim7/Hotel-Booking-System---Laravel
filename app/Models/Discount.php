<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Discount extends Model
{
    protected $fillable = [
        'coupen_code',
        'country_id',
        'hotel_id',
        'type',
        'value',
        'required_code',
        'message',
        'active_status',
        'starts_from',
        'ends_at',
        'min_nights',
        'max_discount',
        'min_amount',
        'usage_limit',
        'used_count',
        'user_limit',
    ];

    protected $casts = [
        'starts_from' => 'datetime',
        'ends_at' => 'datetime',
    ];


    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function getCoupenCodeAttribute($value)
    {
        return strtoupper($value);
    }

    // public function setMaxAmountAttribute($value)
    // {
    //     if ($this->type === 'fixed')
    //         return $this->value;
    // }

    public function getFormattedValueAttribute()
    {
        if ($this->type === 'percentage')
            return $this->value . ' %';
        return $this->value . ' ' . $this->country?->currency_symbol;
    }


    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
