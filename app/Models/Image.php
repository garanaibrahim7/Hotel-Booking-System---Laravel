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
}
