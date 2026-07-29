<?php
namespace App\Services;
use \Illuminate\Http\RedirectResponse;

class ProfileService extends GuzzleApiService
{
    public function dashboard(){

    // --- ADD THESE LOGS TO INSPECT EVERYTHING ---
    logger('--- DEBUGGING DASHBOARD SESSION ---');
    logger('Full Session Payload:', session()->all());

        $token = session()->get('user.token') ?? session()->get('auth_token');
        logger('ProfileService dashboard called. Token status: ' . ($token ? 'Present' : 'Null'));

        if($token==null) {
            logger('Token is null. Redirecting to login.');
            return redirect()->route('login');
        }
        $role = session()->get('user.role');

        if(session()->get('role') == 'super_admin'){
            logger('Executing Super Admin dashboard branch.');
            $response = $this->get('super-admin/dashboard');

            if ($response instanceof RedirectResponse) {
                return $response;
            }
            if (!$response['success']) {
                logger()->error('Super Admin API failed: ' . ($response['message'] ?? 'Unknown error'));
                session()->forget('user');
                session()->forget('decrypt_token');
                return redirect()->back()
                ->withErrors(isset($response['errors']) ? $response['errors'] : ['error'=> $response['message']])
                ->withInput();
            }

            $data = $response['data'];

            return view('superAdmin.dashboard', compact('data'));
        }
        logger('Executing Regular Company Admin dashboard branch.');
        $permissions = session('permissions', []);

        // if (!in_array('dashboard.read', $permissions, true)) {
        //     return view('compaines.dashboard.dashboard');
        // }




        return view('compaines.dashboard');

    }
}