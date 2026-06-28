<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Image extends Model
{
    protected $fillable = [
        'path',
        'created_at',
        'updated_at',
    ];

    public function show(): string
    {
        return "<img src='" . $this->path . "' />";
    }

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getUrlAttribute(): string
    {
        if ($this->path && file_exists(public_path('storage/' . $this->path))) {
            return asset('storage/' . $this->path);
        }

        return $this->imageable_type === Hotel::class 
            ? asset('storage/hotel_placeholder.jpg') 
            : asset('storage/room_placeholder.jpeg');
    }
}
