<?php

namespace App\Models\Merchant;

use Illuminate\Database\Eloquent\Model;

class RoleUser extends Model {
    
    protected $connection = 'mysql2';
    protected $table = "role_user";
    protected $guarded = [];
}
