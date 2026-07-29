    <?php

    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\AuthController;
    use App\Http\Controllers\PasswordSetupController;
    use App\Http\Middleware\EnsureFrontendAuthenticated;
    use App\Http\Middleware\RedirectIfFrontendAuthenticated;
    use App\Http\Controllers\SuperAdminController;
    use App\Http\Controllers\OrgAdminController;
    use App\Http\Controllers\CompanyController;

    Route::get('/', [CompanyController::class, 'dashboard']);


    Route::get('/login', [AuthController::class, 'getLogin'])->name('login');
    // Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
     Route::post('/login', [AuthController::class, 'postLogin'])->name('login.submit');
    Route::get('redirectToLogin',[AuthController::class,'redirectToLogin'])->name('redirectToLogin');

    //TODO:::::::::::
    // Route::post('login',[AuthController::class,'postLogin'])->name('login.post');


    // Account activation / Set Password link from invite email
    // MUST be outside RedirectIfFrontendAuthenticated so logged-in admins testing links aren't bumped to dashboard!
    Route::get('/set-password', [PasswordSetupController::class, 'showForm'])->name('password.set');
    Route::post('/set-password', [PasswordSetupController::class, 'submit'])->name('password.submit');

    // Protected Super Admin Routes
    Route::middleware([EnsureFrontendAuthenticated::class])->group(function () {

        // Route 1: The standard Dashboard (Shows welcome message)
        // Route::get('/dashboard', function () {
        //     return view('dashboard'); // Your welcome view
        // })->name('dashboard');
        Route::get('/dashboard',[CompanyController::class,'dashboard'])->name('dashboard');

        // Route 2: The Organizations page (Shows the organization management table)
        Route::get('/organizations', [OrgAdminController::class, 'index'])->name('organizations.index');

        // Org Admin Invite Routes
        Route::get('/org-admin/create', [OrgAdminController::class, 'createOrgAdmin']);
        Route::get('/org-admins/invite', [OrgAdminController::class, 'createOrgAdmin'])->name('org-admins.invite');
        Route::post('/org-admins/invite', [OrgAdminController::class, 'storeOrgAdmin'])->name('org-admins.store');

        // New Dynamic Action Routes
        Route::put('/organizations/{id}', [OrgAdminController::class, 'updateOrgAdmin'])->name('organizations.update');
        Route::delete('/organizations/{id}', [OrgAdminController::class, 'destroyOrgAdmin'])->name('organizations.destroy');
        Route::post('/organizations/{id}/resend', [OrgAdminController::class, 'resendInvite'])->name('organizations.resend');

        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });
