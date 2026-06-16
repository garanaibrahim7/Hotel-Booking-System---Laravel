<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Transaction extends Model
{
    protected $fillable = [
        'transactionable_id',
        'transactionable_type',
        'note',
        'amount',
        'converted_amount',
        'currency',
        'converted_currency',
        'exchange_rate',
        'mode',
        'type',
        'tax',
        'tax_amount',
        'hash',
    ];

    public function transactionable(): MorphTo
    {
        return $this->morphTo();
    }
}
