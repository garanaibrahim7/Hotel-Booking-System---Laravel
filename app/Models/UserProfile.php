<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class UserProfile extends Model
{
    protected $fillable = [
        'gender',
        'dob',
        'id_type',
        'id_number',
        'address',
        'city_id',
        'pincode'
    ];

    public function pic(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}
