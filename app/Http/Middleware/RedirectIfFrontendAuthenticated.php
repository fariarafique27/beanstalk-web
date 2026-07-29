<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfFrontendAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check using user.token to match your new authentication standard
        $token = session()->get('user.token');

        if ($token !== null) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}