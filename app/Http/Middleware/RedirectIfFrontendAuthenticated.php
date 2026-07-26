<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfFrontendAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        // If already logged in, don't let them hit /login again
        if (session()->has('is_logged_in') && session()->has('auth_token')) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}