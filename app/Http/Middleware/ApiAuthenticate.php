<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ApiKey;

class ApiAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {     
 

        if(!$request->header('authorization')){
            
            return response()->json(['responseMessage' => 'unauthorized access denied', 'responseCode' => 401], 401);
        
        } elseif($request->header('authorization')){
            
            if(count(explode(' ', $request->header('authorization'))) == 2){
                
            
                $api = explode(' ', $request->header('authorization'))[1];
                
                $prefix = explode(' ', $request->header('authorization'))[0];
                
                
                if($prefix != "Bearer"){
                    
                    return response()->json(['responseMessage' => 'unauthorized access denied', 'responseCode' => 401], 401);
                    
                }
                
                if(ApiKey::where('apikey', $api)->exists() == false){
                    
                    return response()->json(['responseMessage' => 'api key not found', 'responseCode' => 404], 404);
                    
                }
            } else {
                
                return response()->json(['responseMessage' => 'unauthorized access denied', 'responseCode' => 401], 401);
                
            }
            
        }
        
        return $next($request);
    }
}
