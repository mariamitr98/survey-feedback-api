<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\RateLimiter;

class RateLimiterMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $limiter_key = 'rate_limiter:' . $request->ip();

        if (RateLimiter::tooManyAttempts($limiter_key, $perMinute = 5)) {
            return response()->json(['error' => 'Too many attempts'], 429);
        }

        RateLimiter::increment($limiter_key);

        return $next($request);
    }
}
