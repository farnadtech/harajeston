<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetUtf8Header
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        $response->headers->set('Content-Type', 'text/html; charset=UTF-8');
        
        return $response;
    }
}
