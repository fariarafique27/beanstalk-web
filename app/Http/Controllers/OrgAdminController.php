<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Services\OrgAdminService;

class OrgAdminController extends Controller
{
    protected $authService;
    protected $orgAdminService;

    public function __construct(OrgAdminService $orgAdminService , AuthService $authService )
    {
        $this->authService = $authService;
        $this->orgAdminService = $orgAdminService;
    }

    public function index()
    {
        try {
            return $this->orgAdminService->getOrganizations(); 
        } catch (\Throwable $e) {
            return $this->getException($e);
        }
    }


    public function createOrgAdmin()
    {
        $userData = session('user', []);
        $userRole = session('role') ?? $userData['role'] ?? 'admin';
        $userPerms = session('permissions', $userData['permissions'] ?? []);

        // ADD IT HERE (For create form unauthorized check)
        if ($userRole !== 'super_admin' && $userRole !== 'super-admin' && !in_array('org-admins.invite', $userPerms)) {
            return redirect()->route('dashboard')
                ->withErrors(['error' => 'Unauthorized action. Super Admin privileges required.']);
        }

        // --- FETCH PERMISSIONS FROM YOUR BACKEND API ---
        $response = \Illuminate\Support\Facades\Http::get('http://127.0.0.1:8000/api/permissions');
        $permissions = $response->successful() ? $response->json('data') : [];

       // return view('org-admins.invite');
       return view('org-admins.invite', compact('permissions'));
    }

    

    public function storeOrgAdmin(Request $request)
    {
        logger('--- 1. STORE ORG ADMIN CALLED ---', [
            'all_input' => $request->all(),
            'permissions' => $request->input('permissions')
        ]);

        $userData = session('user', []);
        $userRole = session('role') ?? $userData['role'] ?? 'admin';
        $userPerms = session('permissions', $userData['permissions'] ?? []);

        // DEBUG: See exactly what role and perms are being read right now
        logger('CURRENT USER CHECK:', ['role' => $userRole, 'perms' => $userPerms]);

        // Temporarily change this condition or force your role to pass
        if ($userRole !== 'super_admin' && $userRole !== 'super-admin' && $userRole !== 'admin') {
            logger('--- 2. UNAUTHORIZED ACCESS ATTEMPT ---', ['role' => $userRole]);
            return redirect()->route('dashboard')
                ->withErrors(['error' => 'Unauthorized action.']);
        }

        logger('--- 3. SENDING REQUEST TO AUTH SERVICE ---', $request->all());

        $response = $this->authService->post('org-admins/invite', $request->all());
        //dd($response);

        logger('--- 4. RESPONSE RECEIVED FROM AUTH SERVICE ---', ['response' => $response]);

        if (!$response['success']) {
            return redirect()->back()
                ->withErrors($response['errors'] ?? ['error' => $response['message']])
                ->withInput();
        }

        return redirect()->route('organizations.index')->with('success', $response['message']);
    }


    // Update Organization / Admin Details
    public function updateOrgAdmin(Request $request, $id)
    {
        $userData = session('user', []);
        $userRole = session('role') ?? $userData['role'] ?? 'admin';
        $userPerms = session('permissions', $userData['permissions'] ?? []);

        if ($userRole !== 'super_admin' && $userRole !== 'super-admin' && !in_array('manage_organizations', $userPerms)) {
            return redirect()->route('dashboard')
                ->withErrors(['error' => 'Unauthorized action.']);
        }

        $response = $this->authService->put("organizations/{$id}", $request->all());

        if (!($response['success'] ?? false)) {
            return redirect()->back()
                ->withErrors($response['errors'] ?? ['error' => $response['message'] ?? 'Update failed.'])
                ->withInput();
        }

        return redirect()->route('organizations.index')
            ->with('success', $response['message'] ?? 'Organization updated successfully.');
    }

    // Suspend / Delete Organization
    public function destroyOrgAdmin($id)
    {
        $userData = session('user', []);
        $userRole = session('role') ?? $userData['role'] ?? 'admin';
        $userPerms = session('permissions', $userData['permissions'] ?? []);

        if ($userRole !== 'super_admin' && $userRole !== 'super-admin' && !in_array('manage_organizations', $userPerms)) {
            return redirect()->route('dashboard')
                ->withErrors(['error' => 'Unauthorized action.']);
        }

        $response = $this->authService->delete("organizations/{$id}");

        if (!($response['success'] ?? false)) {
            return redirect()->back()
                ->withErrors(['error' => $response['message'] ?? 'Failed to delete organization.']);
        }

        return redirect()->route('organizations.index')
            ->with('success', $response['message'] ?? 'Organization removed successfully.');
    }

    // Resend Activation Email
    public function resendInvite($id)
    {
        $userData = session('user', []);
        $userRole = session('role') ?? $userData['role'] ?? 'admin';
        $userPerms = session('permissions', $userData['permissions'] ?? []);

        if ($userRole !== 'super_admin' && $userRole !== 'super-admin' && !in_array('org-admins.invite', $userPerms)) {
            return redirect()->route('dashboard')
                ->withErrors(['error' => 'Unauthorized action.']);
        }

        $response = $this->authService->post("organizations/{$id}/resend-invite", []);

        if (!($response['success'] ?? false)) {
            return redirect()->back()
                ->withErrors(['error' => $response['message'] ?? 'Failed to resend invitation email.']);
        }

        return redirect()->route('organizations.index')
            ->with('success', $response['message'] ?? 'Invitation email dispatched successfully.');
    }

}