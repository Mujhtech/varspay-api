<?php

namespace App\Models\Merchant;

use Illuminate\Database\Eloquent\Model;

class PermissionRole extends Model {
    
    protected $connection = 'mysql2';
    protected $table = "permission_role";
    protected $guarded = [];
}
