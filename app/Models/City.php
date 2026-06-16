<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    protected $fillable = [
        'state_id',
        'name'
    ];


    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function hotels(): HasMany
    {
        return $this->hasMany(Hotel::class);
    }


    protected function locationDetails(): Attribute
    {
        return Attribute::make(
            get: fn() => (object)[
                'city' => $this->name,
                'state' => $this->state?->name ?? 'N/A',
                'country' => $this->state?->country?->name ?? 'N/A',
            ],
        );
    }
}
