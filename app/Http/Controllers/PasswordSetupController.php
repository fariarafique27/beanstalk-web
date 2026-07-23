<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PasswordSetupController extends Controller
{
    public function showForm(Request $request)
    {
        // Return your set-password view here
        return view('auth.set-password');
    }

    public function submit(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        // Just assign the plain text password. Laravel's 'password' => 'hashed' cast handles the rest automatically!
        $user->password = $request->password;
        $user->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('status', 'Password set successfully! Please log in.');
    }
}