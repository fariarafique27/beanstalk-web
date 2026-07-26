<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;

class OrgAdminController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    
    public function index()
    {
        $userData = session('user', []);
        $userRole = session('role') ?? $userData['role'] ?? 'admin';
        $userPerms = session('permissions', $userData['permissions'] ?? []);

        if ($userRole !== 'super_admin' && $userRole !== 'super-admin' && !in_array('read_organizations', $userPerms) && !in_array('manage_organizations', $userPerms)) {
            return redirect()->route('dashboard')
                ->withErrors(['error' => 'You do not have permission to access Organizations.']);
        }

        $response = $this->authService->get('organizations'); 

        // --- TEMPORARY DEBUGGING LINE ---
        // Uncomment the line below to view the exact structure of your API response on the screen:
        // dd($response);

        // Flexible fallback keys to catch whatever structure your backend returns:
        $organizations = $response['organizations'] ?? $response['data']['organizations'] ?? $response['data'] ?? [];
        
        // If it's a paginator object or nested collection, normalize it to an array:
        if (is_object($organizations)) {
            $organizations = json_decode(json_encode($organizations), true);
        }
        
        if (!is_array($organizations)) {
            $organizations = [];
        }

        $stats = $response['stats'] ?? $response['data']['stats'] ?? [
            'total_orgs' => count($organizations),
            'active_admins' => collect($organizations)->where('status', 'active')->count(),
            'pending_invites' => collect($organizations)->where('status', '!=', 'active')->count()
        ];

        return view('organizations.index', compact('organizations', 'stats'));
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

        return view('org-admins.invite');
    }

    public function storeOrgAdmin(Request $request)
    {
        $userData = session('user', []);
        $userRole = session('role') ?? $userData['role'] ?? 'admin';
        $userPerms = session('permissions', $userData['permissions'] ?? []);

        if ($userRole !== 'super_admin' && $userRole !== 'super-admin' && !in_array('org-admins.invite', $userPerms)) {
            return redirect()->route('dashboard')
                ->withErrors(['error' => 'Unauthorized action.']);
        }

        $response = $this->authService->post('org-admins/invite', $request->all());

        if (!$response['success']) {
            return redirect()->back()
                ->withErrors($response['errors'] ?? ['error' => $response['message']])
                ->withInput();
        }

        // ADD IT HERE (For successful form submission)
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