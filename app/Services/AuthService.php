<?php

namespace App\Services;


class AuthService extends GuzzleApiService
{
    public function getLogin(){
        $token = session()->get('user.token');
        if($token==null) {
            return view('auth.login');
        }

        return redirect()->route('dashboard');
    }

}