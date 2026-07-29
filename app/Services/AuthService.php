<?php

namespace App\Services;


class AuthService extends GuzzleApiService
{

    public function getLogin(){
        logger("called getLogin ");
        $token = session()->get('user.token');
        if($token==null) {
            return view('auth.login');
        }

        return redirect()->route('dashboard');
    }

    public function redirectToLogin(){
        return redirect()->route('login')->withErrors(['error' => 'Session expired. Please log in again.']);
    }

    public function postLogin($request){
        logger("called postLogin ");
        $credentials = [
            'email'    => $request->email,
            'password' => $request->password,
        ];

        $response  =  $this->post('login', $credentials);

        try{

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
        

        // copy paste code 
        // if ($response instanceof \Illuminate\Http\RedirectResponse) {
        //     return $response;
        // }

        // if (!$response['success']) {
        //     return redirect()->back()
        //         ->withErrors(isset($response['errors']) ? $response['errors'] : ['error' => $response['message']])
        //         ->withInput();
        // }
        // session([
        //     'user' => $response['data']['user'],
        //     'permissions' => $response['data']['permissions'],
        //   //  'chatbot_status'=>$response['data']['chatBot_status']
        // ]);

        // return redirect()->intended(route('dashboard'));

        
    }


    // Add this method to handle POST requests via Guzzle
    public function login(array $credentials)
    {
        // This will call the post() method in GuzzleApiService, which automatically logs it!
        return $this->post('login', $credentials);
    }

}