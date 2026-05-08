<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingItem extends Model
{
    // Mass assignable attributes
    protected $fillable = [
        'booking_id',
        'room_id',
        'check_in',
        'check_out',
        'price_at_booking'
    ];

    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
    ];

    // Each item belongs to the main booking
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    // Each item represents one specific room
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
