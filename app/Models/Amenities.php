<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Amenities extends Model
{

    protected $fillable = [
        'icon',
        'title',
        'amenityable_id',
        'amenityable_type',
    ];

    public function amenityable(): MorphTo
    {
        return $this->morphTo();
    }
}
