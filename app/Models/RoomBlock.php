<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomBlock extends Model
{
    protected $fillable = [
        'room_detail_id',
        'from',
        'to',
        'reason',
        'created_at',
        'updated_at',
    ];

    public function details(): BelongsTo
    {
        return $this->belongsTo(RoomDetail::class, 'room_detail_id');
    }
}
