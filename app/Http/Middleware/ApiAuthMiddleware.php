<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiAuthMiddleware
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
        $token = $request->bearerToken();
        $validToken = env('API_TOKEN');

        if (!$token || $token !== $validToken) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or missing API Token',
                'status_code' => 401
            ], 401);
        }

        return $next($request);
    }
}
