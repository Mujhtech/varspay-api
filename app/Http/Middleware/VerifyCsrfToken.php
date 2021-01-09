<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    protected $addHttpCookie = true;
    
    protected $except = [
        'ext_transfer',
        '/webhook/providus/bulk_transfer',
    ];
}
