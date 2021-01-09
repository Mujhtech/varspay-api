<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{

    public function handle($request, Closure $next, $guard = null)
    {
        if ($guard == "user" && Auth::guard($guard)->check()) {
            return redirect('/user/dashboard');
        }else{
            Auth::guard()->logout();
            return redirect('/login');
        } 
        
        if ($guard == "admin" && Auth::guard($guard)->check()) {
            return redirect('/admin/dashboard');
        }else{
            Auth::guard()->logout();
            return redirect('/admin');
        }

        return $next($request);
    }
}
