<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * If the user is not authenticated, redirect to the login page.
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {
        if (! Auth::guard($guard)->check()) {
            // If request expects JSON, return 401 JSON response
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return redirect()->route('login');
        }

        return $next($request);
    }
}
