<?php

namespace App\Services;


class AuthService extends GuzzleApiService
{
    // public function getLogin(){
    //     $token = session()->get('user.token');
    //     if($token==null) {
    //         return view('auth.login');
    //     }

    //     return redirect()->route('dashboard');
    // }

    public function getLogin(){
        $token = session()->get('user.token');
        if($token == null) {
            return view('auth.login');
        }

        return redirect()->route('dashboard');
    }

    // Add this method to handle POST requests via Guzzle
    public function login(array $credentials)
    {
        // This will call the post() method in GuzzleApiService, which automatically logs it!
        return $this->post('login', $credentials);
    }

}