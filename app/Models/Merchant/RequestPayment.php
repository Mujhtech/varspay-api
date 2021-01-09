<?php

namespace App\Models\Merchant;

use Illuminate\Database\Eloquent\Model;

class RequestPayment extends Model {
    
    protected $connection = 'mysql2';
    protected $table = "request_payments";
    protected $guarded = [];
}
