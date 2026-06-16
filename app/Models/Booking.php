<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    const STATUS_PENDING = 0;
    const STATUS_CONFIRMED = 1;
    const STATUS_FAILED = 2;
    const STATUS_PROCESSING = 3;
    const STATUS_CANCELLED = 4;
    const STATUS_REJECTED = 5;
    const STATUS_REFUNDED = 6;

    protected $fillable = [
        'user_id',
        'hotel_id',
        'status',
        'reference_number',
        'total_amount',
        'sub_amount',
        'discount_id',
        'discount_amount',
        'instructions',
        'currency',
        'guest_name',
        'guest_email',
        'guest_phone',
        'arrival',
        'leaved',
    ];

    protected $casts = [
        'arrival' => 'datetime',
        'leaved' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BookingItem::class);
    }

    public function refund(): HasOne
    {
        return $this->hasOne(Refund::class);
    }

    public function getStayDatesAttribute()
    {
        return [
            'check_in' => $this->items->first()?->check_in->format('d-m-Y'),
            'check_out' => $this->items->first()?->check_out->format('d-m-Y'),
        ];
    }

    // public function isConfirmed(): bool
    // {
    //     return $this->status === self::STATUS_CONFIRMED;
    // }
}
