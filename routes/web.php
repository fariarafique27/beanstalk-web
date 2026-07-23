<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordSetupController;
use App\Http\Middleware\EnsureFrontendAuthenticated;
use App\Http\Middleware\RedirectIfFrontendAuthenticated;
use App\Http\Controllers\SuperAdminController;

// Redirect root to dashboard or login
Route::get('/', function () {
    return session('is_logged_in') 
        ? redirect()->route('super-admin.dashboard') 
        : redirect()->route('login');
});

// Guest Routes (Public)
Route::middleware([RedirectIfFrontendAuthenticated::class])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Account activation / Set Password link from invite email
// MUST be outside RedirectIfFrontendAuthenticated so logged-in admins testing links aren't bumped to dashboard!
Route::get('/set-password', [PasswordSetupController::class, 'showForm'])->name('password.set');
Route::post('/set-password', [PasswordSetupController::class, 'submit'])->name('password.submit');

// Protected Super Admin Routes
Route::middleware([EnsureFrontendAuthenticated::class])->group(function () {

    Route::get('/super-admin/dashboard', [SuperAdminController::class, 'index'])
        ->name('super-admin.dashboard');

    Route::post('/org-admins/invite', [SuperAdminController::class, 'inviteOrgAdmin'])
        ->name('org-admins.invite');

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});