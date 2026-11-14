
<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ShortUrlRedirectController;

use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Member\DashboardController as MemberDashboardController;


/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register'])
        ->name('register.store');

    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.store');

});


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');


    /*
    | Common Dashboard Redirect
    */

    Route::get('/dashboard', function () {

        $user = auth()->user();

        if ($user->role === 'superadmin') {
            return redirect()->route('superadmin.dashboard');
        }

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role === 'member') {
            return redirect()->route('member.dashboard');
        }

        abort(403);

    })->name('dashboard');


    /*
    | SuperAdmin Routes
    */

    Route::middleware('role:superadmin')
        ->prefix('superadmin')
        ->name('superadmin.')
        ->group(function () {

            Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])
                ->name('dashboard');

            Route::get('/invite', [SuperAdminDashboardController::class, 'invite'])
                ->name('invite');

            Route::post('/invite', [SuperAdminDashboardController::class, 'storeInvitation'])
                ->name('invite.store');

            Route::get('/invitations', [SuperAdminDashboardController::class, 'viewAllInvitations'])
                ->name('invitations.index');

            Route::get('/short-urls', [SuperAdminDashboardController::class, 'viewAllShortUrls'])
                ->name('shorturls.index');

            Route::get('/short-urls/download', [SuperAdminDashboardController::class, 'downloadShortUrls'])
                ->name('shorturls.download');

        });


    /*
    | Admin Routes
    */

    Route::middleware('role:admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            Route::get('/dashboard', [AdminDashboardController::class, 'index'])
                ->name('dashboard');

            // Invite Admin or Member
            Route::get('/invite', [AdminDashboardController::class, 'invite'])
                ->name('invite');

            Route::post('/invite', [AdminDashboardController::class, 'storeInvitation'])
                ->name('invite.store');

            // Generate Short URL
            Route::get('/generate-short-url', [AdminDashboardController::class, 'createShortUrl'])
                ->name('shorturl.create');

            Route::post('/generate-short-url', [AdminDashboardController::class, 'generateShortUrl'])
                ->name('shorturl.store');

            // Short URL List
            Route::get('/short-urls', [AdminDashboardController::class, 'viewAllShortUrls'])
                ->name('shorturls.index');

            // Invitation List
            Route::get('/invitations', [AdminDashboardController::class, 'viewAllInvitations'])
                ->name('invitations.index');

            // Download Short URLs
            Route::get('/short-urls/download', [AdminDashboardController::class, 'downloadShortUrls'])
                ->name('shorturls.download');

        });


    /*
    | Member Routes
    */

    Route::middleware('role:member')
        ->prefix('member')
        ->name('member.')
        ->group(function () {

            Route::get('/dashboard', [MemberDashboardController::class, 'index'])
                ->name('dashboard');

            // Generate Short URL Form
            Route::get('/generate-short-url', function () {
                return view('member.short-url');
            })->name('shorturl.create');

            Route::post('/generate-short-url', [MemberDashboardController::class, 'generateShortUrl'])
                ->name('shorturl.store');

            // Download Short URLs
            Route::get('/short-urls/download', [MemberDashboardController::class, 'downloadShortUrls'])
                ->name('shorturls.download');

        });

});


/*

| Public Short URL Redirect

*/

Route::get('/{short_code}', [ShortUrlRedirectController::class, 'redirect'])
    ->where('short_code', '[A-Za-z0-9]+')
    ->name('shorturl.redirect');


/*
| Home Page
*/

Route::get('/', function () {
    return redirect()->route('login');
});
