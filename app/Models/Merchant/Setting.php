<?php

namespace App\Models\Merchant;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model {
    
    protected $connection = 'mysql2';
    protected $table = "settings";
    protected $guarded = [];
}
