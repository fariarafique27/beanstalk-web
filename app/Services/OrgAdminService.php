<?php

namespace App\Services;

class OrgAdminService extends GuzzleApiService
{
    /**
     * Get organizations with permission check and return view
     * 
     * @return \Illuminate\View\View
     */
    public function getOrganizations()
    {
        // Check permissions
        $userData = session('user', []);
        $userRole = session('role') ?? $userData['role'] ?? 'admin';
        $userPerms = session('permissions', $userData['permissions'] ?? []);

        if ($userRole !== 'super_admin' 
            && !in_array('read_organizations', $userPerms) 
            && !in_array('manage_organizations', $userPerms)) {
            throw new \Exception('You do not have permission to access Organizations.');
        }

        // Fetch organizations from backend API
        $response = $this->get('organizations');

        logger('Organizations API Response:', $response);

        // Handle error responses
        if (!isset($response['success']) || !$response['success']) {
            throw new \Exception($response['message'] ?? 'Failed to fetch organizations');
        }

        // Extract data from backend response structure
        // Backend returns: { success, message, data: { organizations, stats } }
        $data = $response['data'] ?? [];
        $organizations = $data['organizations'] ?? [];
        $stats = $data['stats'] ?? [];

        // Convert objects to arrays if needed
        if (is_object($organizations)) {
            $organizations = json_decode(json_encode($organizations), true);
        }

        // Ensure it's always an array
        if (!is_array($organizations)) {
            $organizations = [];
        }

        // Transform organizations to match table structure
        $organizations = $this->transformOrganizations($organizations);

        // Fetch permissions for modal
        $permissions = $this->getPermissions();

        logger('Transformed Organizations:', $organizations);
        logger('Stats:', $stats);

        // Return view with all data
        return view('organizations.index', [
            'organizations' => $organizations,
            'stats' => $stats,
            'permissions' => $permissions
        ]);
    }

    /**
     * Transform backend organization structure to match table display
     * 
     * @param array $organizations
     * @return array
     */
    private function transformOrganizations(array $organizations): array
    {
        return collect($organizations)->map(function ($org) {
            // Get first user/admin
            $admin = null;
            if (isset($org['users']) && is_array($org['users']) && count($org['users']) > 0) {
                $admin = $org['users'][0];
            }

            return [
                'id' => $org['id'] ?? null,
                'org_name' => $org['name'] ?? 'N/A',
                'admin_name' => $admin['name'] ?? 'Unassigned',
                'admin_email' => $admin['email'] ?? 'N/A',
                'status' => $org['status'] == 1 ? 'active' : 'pending',
                'permissions' => $org['permissions'] ?? [],
                'users' => $org['users'] ?? [],
                'email' => $org['email'] ?? 'N/A',
            ];
        })->toArray();
    }

    /**
     * Get all permissions from backend
     * 
     * @return array
     */
    private function getPermissions(): array
    {
        try {
            $response = $this->get('permissions');
            
            if (isset($response['success']) && $response['success']) {
                return $response['data'] ?? [];
            }
        } catch (\Exception $e) {
            logger()->warning('Failed to fetch permissions', ['error' => $e->getMessage()]);
        }

        return [];
    }
}