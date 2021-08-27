<?php

namespace FherryFherry\LaravelApiToken\Middleware;

use Closure;
use FherryFherry\LaravelApiToken\Helper\LaravelSimpleApiToken;
use Illuminate\Http\Request;

class ApiTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(LaravelSimpleApiToken::validateBearerToken($request)) {
            return $next($request);
        } else {
            return response()->json(['status'=>0,'message'=>'Token is expired'],401);
        }
    }
}
