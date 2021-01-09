<?php

namespace App\Models\Merchant;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model {
    
    protected $connection = 'mysql2';
    protected $table = "permissions";
    protected $guarded = [];
}
