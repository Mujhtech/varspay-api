<?php

namespace App\Models\Merchant;

use Illuminate\Database\Eloquent\Model;

class User extends Model {
    
    protected $connection = 'mysql2';
    protected $table = "users";
    protected $guarded = [];
}
