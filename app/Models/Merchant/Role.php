<?php

namespace App\Models\Merchant;

use Illuminate\Database\Eloquent\Model;

class Role extends Model {
    
    protected $connection = 'mysql2';
    protected $table = "roles";
    protected $guarded = [];
}
