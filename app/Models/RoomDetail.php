<?php

namespace App\Models;

use Arr;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Log;
use Str;

class RoomDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'hotel_id',
        'type',
        'category',
        'description',
        'qty',
        'price',
        'max_adults',
        'max_children',
    ];

    public function getTitleAttribute()
    {
        return Str::title($this->category) . " - " . Str::title($this->type);
    }

    public function getLocalPriceAttribute()
    {
        $country = $this->hotel->city->state->country;
        return $this->price . " " . $country->currency_symbol . " (" . $country->currency_code . ")";
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(RoomBlock::class);
    }
}
