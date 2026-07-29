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
        // if (!session()->has('is_logged_in') || !session()->has('auth_token')) {
        //     return redirect()->route('login')->withErrors([
        //         'auth' => 'Please log in to access the dashboard.',
        //     ]);
        // }

        // return $next($request);

       //  dd("this is ddddddddddddddddddddddd" , session()->all());
         $token = session()->get('user.token');

        if ($token === null) {
            // Log the expired or missing session token
            logger('Session expired or user token missing during request.', [
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
                'is_ajax' => $request->ajax(),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session expired. Please log in again.',
                    'url'     => route('redirectToLogin'),  
                ], 401);
            }

            session(['url.intended' => $request->url()]);

            return redirect()->route('login')
                ->withErrors(['error' => 'Session expired. Please log in again.']);
        }

        return $next($request);

    }
}