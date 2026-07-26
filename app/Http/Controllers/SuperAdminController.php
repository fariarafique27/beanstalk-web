<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SuperAdminController extends Controller
{
    /**
     * Display the Super Admin Dashboard with stats and organization data.
     */
    public function index()
    {
        $baseUrl = config('api.backend_url', 'http://127.0.0.1:8000/api');

        try {
            // Call Backend API using the user's active session token
            $response = Http::acceptJson()
                ->withToken(session('auth_token'))
                ->get("{$baseUrl}/super-admin/dashboard");

            // Handle unauthenticated/expired token from backend API
            if ($response->status() === 401) {
                session()->flush();
                return redirect()->route('login')->withErrors([
                    'email' => 'Session expired. Please sign in again.'
                ]);
            }

            $data = $response->json('data') ?? $response->json() ?? [];

            return view('dashboard', [
                'organizations' => $data['organizations'] ?? [],
                'stats'         => $data['stats'] ?? [
                    'total_orgs'      => 0,
                    'active_admins'   => 0,
                    'pending_invites' => 0,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Dashboard API Error: ' . $e->getMessage());

            // Return view with empty data if backend server is unreachable
           return view('dashboard', [
                'organizations' => [],
                'stats'         => ['total_orgs' => 0, 'active_admins' => 0, 'pending_invites' => 0],
                'api_error'     => 'Unable to fetch real-time dashboard data from backend.',
            ]);
        }
    }

    /**
     * Invite a new Organization Admin.
     */
    public function inviteOrgAdmin(Request $request)
    {
        $validated = $request->validate([
            'org_name'    => 'required|string|max:255',
            'name'        => 'required|string|max:255',
            'email'       => 'required|email',
            'permissions' => 'nullable|array',
        ]);

        $baseUrl = config('api.backend_url', 'http://127.0.0.1:8000/api');

        try {
            $response = Http::acceptJson()
                ->withToken(session('auth_token'))
                ->post("{$baseUrl}/org-admins/invite", $validated);

            if ($response->failed()) {
                $errorMessage = $response->json('message') 
                    ?? $response->json('error') 
                    ?? 'Failed to send invitation.';

                return back()->withErrors(['invite_error' => $errorMessage])->withInput();
            }

            return back()->with('success', 'Invitation link successfully sent to ' . $validated['email']);

        } catch (\Exception $e) {
            Log::error('Invite Org Admin Error: ' . $e->getMessage());
            return back()->withErrors(['invite_error' => 'Could not connect to server to send invitation.'])->withInput();
        }
    }
}