<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 1. Call Backend API
        $response = Http::post(config('api.backend_url', 'http://127.0.0.1:8000/api') . '/login', [
            'email' => $request->email,
            'password' => $request->password,
        ]);

        // 2. Handle 401, 403, or 500 errors from backend
        if ($response->failed()) {
            $errorMessage = $response->json('message') 
                ?? $response->json('error') 
                ?? 'Invalid credentials or account inactive.';

            return back()->withErrors([
                'email' => $errorMessage,
            ])->onlyInput('email');
        }

        $resJson = $response->json();

        // 3. Extract payload from the backend's `data` object
        $payload = $resJson['data'] ?? $resJson;

        $token = $payload['token'] ?? null;

        if (!$token) {
            return back()->withErrors(['email' => 'Authentication token was not provided by API.']);
        }

        // 4. Store user data, token, and org details in Session
        session([
            'auth_token'     => $token,
            'user'           => $payload, // Contains user details like name, email, role, etc.
            'index_name'     => $payload['index_name'] ?? null,
            'chatbot_status' => $payload['chatbot_status'] ?? false,
            'is_logged_in'   => true,
        ]);

        // 5. Redirect to Super Admin Dashboard
        return redirect()->route('super-admin.dashboard');
    }

    public function logout(Request $request)
    {
        $token = session('auth_token');

        if ($token) {
            Http::withToken($token)->post(config('api.backend_url', 'http://127.0.0.1:8000/api') . '/logout');
        }

        session()->flush();
        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }
}