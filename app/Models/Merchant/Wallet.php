<?php

namespace App\Models\Merchant;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model {
    
    protected $connection = 'mysql2';
    protected $table = "wallets";
    protected $guarded = [];
}
