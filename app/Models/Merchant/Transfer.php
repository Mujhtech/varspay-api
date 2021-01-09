<?php

namespace App\Models\Merchant;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model {
    
    protected $connection = 'mysql2';
    protected $table = "transfers";
    protected $guarded = [];
}
