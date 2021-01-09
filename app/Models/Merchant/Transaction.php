<?php

namespace App\Models\Merchant;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model {
    
    protected $connection = 'mysql2';
    protected $table = "transactions";
    protected $guarded = [];
    
    public function transaction_type()
    {
        return $this->belongsTo(TransactionType::class);
    }
}
