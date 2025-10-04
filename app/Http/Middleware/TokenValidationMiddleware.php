<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TokenValidationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user) {
            if (app('token')->ip_address === get_client_ip()) {
                return $next($request);
            }
        }

        return response()->json(['message' => __('messages.unauthorized_access')], 401);
    }
}
