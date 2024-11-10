<?php

namespace App\Tools;

use App\Models\Transaction;

use function PHPUnit\Framework\isEmpty;

class Utility{

    public function updateIfNotNullTransaction(Transaction $transaction, string $field, string $value){
        if($value != "" && $value != null){
            $transaction->$field=$value;
        }
    }
    
}