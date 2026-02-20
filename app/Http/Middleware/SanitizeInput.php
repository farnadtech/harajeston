<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SanitizeInput
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Sanitize all input except password fields
        $input = $request->except(['password', 'password_confirmation']);
        
        $sanitized = $this->sanitizeArray($input);
        
        $request->merge($sanitized);

        return $next($request);
    }

    /**
     * Recursively sanitize array
     */
    protected function sanitizeArray(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->sanitizeArray($value);
            } elseif (is_string($value)) {
                // Remove potentially dangerous characters
                $data[$key] = strip_tags($value);
            }
        }

        return $data;
    }
}
