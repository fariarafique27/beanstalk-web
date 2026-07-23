<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureFrontendAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if session has active login token
        if (!session()->has('is_logged_in') || !session()->has('auth_token')) {
            return redirect()->route('login')->withErrors([
                'auth' => 'Please log in to access the dashboard.',
            ]);
        }

        return $next($request);
    }
}