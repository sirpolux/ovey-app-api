<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    //

    protected $fillable = [
        'account_id',
        'amount',
        'date_paid',
        'transaction_type',
        'purpose',
        'created_by'
    ];
}
