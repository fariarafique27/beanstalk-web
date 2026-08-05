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

        // Check if user has permission
        if ($userRole !== 'super_admin' 
            && !in_array('read_organizations', $userPerms) 
            && !in_array('manage_organizations', $userPerms)) {
            throw new \Exception('You do not have permission to access Organizations.');
        }

        // Fetch organizations from API
        $response = $this->get('organizations');

        // Handle error responses
        if (isset($response['success']) && !$response['success']) {
            throw new \Exception($response['message'] ?? 'Failed to fetch organizations');
        }

        // Normalize data - flexible fallback keys
        $organizations = $response['organizations'] 
            ?? $response['data']['organizations'] 
            ?? $response['data'] 
            ?? [];

        // Convert objects to arrays if needed
        if (is_object($organizations)) {
            $organizations = json_decode(json_encode($organizations), true);
        }

        // Ensure it's always an array
        if (!is_array($organizations)) {
            $organizations = [];
        }

        // Calculate stats
        $stats = $response['stats'] 
            ?? $response['data']['stats'] 
            ?? $this->calculateStats($organizations);

        // Fetch permissions
        $permissions = $this->getPermissions();

        // Return view with all data
        return view('organizations.index', [
            'organizations' => $organizations,
            'stats' => $stats,
            'permissions' => $permissions
        ]);
    }

    /**
     * Calculate organization statistics
     * 
     * @param array $organizations
     * @return array
     */
    private function calculateStats(array $organizations): array
    {
        return [
            'total_orgs' => count($organizations),
            'active_admins' => collect($organizations)
                ->where('status', 'active')
                ->count(),
            'pending_invites' => collect($organizations)
                ->where('status', '!=', 'active')
                ->count()
        ];
    }

    /**
     * Fetch permissions from the API
     * 
     * @return array
     */
    public function getPermissions()
    {
        try {
            $response = \Illuminate\Support\Facades\Http::get(
                config('api.base_url') . 'permissions'
            );

            return $response->successful() ? $response->json('data', []) : [];
        } catch (\Exception $e) {
            logger()->warning('Failed to fetch permissions: ' . $e->getMessage());
            return [];
        }
    }
}