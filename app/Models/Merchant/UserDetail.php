<?php

namespace App\Models\Merchant;

use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model {
    
    protected $connection = 'mysql2';
    protected $table = "user_details";
    protected $guarded = [];
}
