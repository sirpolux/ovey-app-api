<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    //

    protected $fillable= [
        'name',
        'card_no',
        'phone_number',
        'address',
        'created_by'
    ];
}
