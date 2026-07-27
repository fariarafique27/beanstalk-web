<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\AuthService;

class AuthController extends Controller
{
    protected $authService;
    
    public function __construct(AuthService $authService){
        $this->authService = $authService;
    }

    public function getLogin(){
        try{
            return $this->authService->getLogin();
        }
        catch(\Throwable $e){
           // return $this->getException($e);
          return $this->authService->getException($e);
        }

    }
    
    // public function showLogin()
    // {
    //     return view('auth.login');
    // }
    
    public function showLogin()
    {
        // If the user is already logged in, redirect them away from the login page
        if (session()->has('user.token')) {
            return redirect()->route('dashboard');
        }

        // Clear out any stray tokens or reset states so they don't trigger redirects
        session()->forget(['token', 'email']); 

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        try {
            // Use your Guzzle-powered AuthService
            $response = $this->authService->login([
                'email'    => $request->email,
                'password' => $request->password,
            ]);

            // If Guzzle returns an error array format from your exception handler
            if (isset($response['success']) && $response['success'] === false) {
                return back()->withErrors(['email' => $response['message'] ?? 'Invalid credentials.'])
                             ->onlyInput('email');
            }

            $payload = $response['data'] ?? $response;
            $token   = $payload['token'] ?? null;

            $roles = $payload['roles'] ?? [];
            $isSuperAdmin = in_array('Super Admin', $roles) || ($payload['is_root'] ?? false);

            // Pull permissions from API, and ensure root/super admins have everything needed
            $permissions = $payload['permissions'] ?? [];
            if ($isSuperAdmin) {
                // Ensure Super Admin has access to invite org admins and manage employees
                $permissions = array_unique(array_merge($permissions, [
                    'org-admins.invite', 
                    'employees.manage'
                ]));
            }

            if (!$token) {
                return back()->withErrors(['email' => 'Authentication token was not provided by API.'])
                             ->onlyInput('email');
            }

            // Store user payload and session state
            session([
                'auth_token'     => $token,
                'user'           => $payload,
                'role'           => $isSuperAdmin ? 'super_admin' : 'admin',
                'permissions'    => $permissions,
                'index_name'     => $payload['index_name'] ?? null,
                'chatbot_status' => $payload['chatbot_status'] ?? false,
                'is_logged_in'   => true,
            ]);

            return redirect()->route('dashboard');

        } catch (\Throwable $e) {
            logger()->error("Guzzle API Login Exception: " . $e->getMessage());
            return back()->withErrors(['email' => 'Unable to connect to authentication server.'])
                         ->onlyInput('email');
        }
    }
    
    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'email'    => 'required|email',
    //         'password' => 'required',
    //     ]);

    //     $baseUrl = config('api.backend_url', 'http://127.0.0.1:8000/api');

    //     try {
    //         // Force JSON headers to ensure API returns structured JSON error responses
    //         $response = Http::acceptJson()->post("{$baseUrl}/login", [
    //             'email'    => $request->email,
    //             'password' => $request->password,
    //         ]);
    //     } catch (\Exception $e) {
    //         Log::error("Backend connection failure: " . $e->getMessage());
    //         return back()->withErrors(['email' => 'Unable to connect to authentication server.'])
    //                      ->onlyInput('email');
    //     }

    //     // Handle HTTP failures (401, 403, 422, 500)
    //     if ($response->failed()) {
    //         $errorMessage = $response->json('message') 
    //             ?? $response->json('error') 
    //             ?? 'Invalid credentials or inactive account.';

    //         return back()->withErrors(['email' => $errorMessage])
    //                      ->onlyInput('email');
    //     }

    //     $resJson = $response->json();
    //     $payload = $resJson['data'] ?? $resJson;
    //     $token   = $payload['token'] ?? null;

    //     if (!$token) {
    //         return back()->withErrors(['email' => 'Authentication token was not provided by API.']);
    //     }

    //     // Store user payload and session state
    //     session([
    //         'auth_token'     => $token,
    //         'user'           => $payload,
    //         'index_name'     => $payload['index_name'] ?? null,
    //         'chatbot_status' => $payload['chatbot_status'] ?? false,
    //         'is_logged_in'   => true,
    //     ]);

    //     return redirect()->route('dashboard');
    // }

    public function logout(Request $request)
    {
        $token   = session('auth_token');
        $baseUrl = config('api.backend_url', 'http://127.0.0.1:8000/api');

        if ($token) {
            try {
                // Fire and forget logout to backend API
                Http::acceptJson()->withToken($token)->post("{$baseUrl}/logout");
            } catch (\Exception $e) {
                Log::warning("Logout API call failed, flushing local session anyway.");
            }
        }

        session()->flush();
        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }
}