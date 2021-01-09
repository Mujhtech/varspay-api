<?php

namespace App\Models\Merchant;

use Illuminate\Database\Eloquent\Model;

class TransactionType extends Model {
    
    protected $connection = 'mysql2';
    protected $table = "transactions";
    protected $guarded = [];

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }
}
