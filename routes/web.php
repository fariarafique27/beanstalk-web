<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\EnsureFrontendAuthenticated;

// Guest Routes (Public)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected Super Admin Routes (Requires Login)
Route::middleware(EnsureFrontendAuthenticated::class)->group(function () {
    
    Route::get('/super-admin/dashboard', function () {
        return view('super-admin.dashboard');
    })->name('super-admin.dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});