<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Hotel extends Model
{
    protected $fillable = [
        'name',
        'description',
        'address',
        'city_id',
        'pincode',
        'cancellation_charge'
    ];

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function amenities(): MorphMany
    {
        return $this->morphMany(Amenities::class, 'amenityable');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function getCountryAttribute(): ?Country
    {
        return $this->city?->state?->country;
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(RoomDetail::class);
    }

    public function getFullAddressAttribute()
    {
        return $this->address . ', ' . $this->city?->name . ', ' . $this->city?->state?->name . ', ' . $this->city?->state?->country?->name;
        return $this->city?->name . ', ' . $this->city?->state?->name . ', ' . $this->city?->state?->country?->name;
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getCoverImageAttribute(): string
    {
        $image = $this->images->first();
        return $image ? $image->url : asset('storage/hotel_placeholder.jpg');
    }
}
