<?php

namespace LaravelApiToken\Middleware;

use Closure;
use Illuminate\Http\Request;
use LaravelApiToken\LaravelSimpleApiToken;

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
