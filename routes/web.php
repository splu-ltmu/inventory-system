<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| AUTH CONTROLLERS
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\ClientAuthController;
use App\Http\Controllers\Auth\LogoutController;

/*
|--------------------------------------------------------------------------
| ADMIN CONTROLLERS
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\StockController as AdminStockController;
use App\Http\Controllers\Admin\InboundController;
use App\Http\Controllers\Admin\OutboundController;
use App\Http\Controllers\Admin\RequestController as AdminRequestController;
use App\Http\Controllers\Admin\PasswordResetController as AdminPasswordResetController;
use App\Http\Controllers\Admin\UserManagementController;

/*
|--------------------------------------------------------------------------
| CLIENT CONTROLLERS
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Client\ClientDashboardController;
use App\Http\Controllers\Client\StockController as ClientStockController;
use App\Http\Controllers\Client\ClientRequestController;
use App\Http\Controllers\Client\AccountController;
use App\Http\Controllers\Client\PasswordResetController as ClientPasswordResetController;

/*
|--------------------------------------------------------------------------
| ROOT
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => redirect('/client/login'));

/*
|--------------------------------------------------------------------------
| PUBLIC AUTH ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');

Route::get('/client/login', [ClientAuthController::class, 'showLogin'])->name('client.login');
Route::post('/client/login', [ClientAuthController::class, 'login'])->name('client.login.submit');

// Default login route (redirect to client login)
Route::get('/login', fn () => redirect('/client/login'))->name('login');

// Admin self-password reset (public, no auth required)
Route::get('/admin/password-reset-self', [AdminPasswordResetController::class, 'showSelfResetForm'])->name('admin.password-reset-self.form');
Route::post('/admin/password-reset-self', [AdminPasswordResetController::class, 'sendResetLink'])->name('admin.password-reset-self.send');

Route::get('/password-reset', [ClientPasswordResetController::class, 'showRequestForm'])->name('password-reset.request');
Route::post('/password-reset', [ClientPasswordResetController::class, 'submitRequest'])->name('password-reset.submit');

// Public token-based reset links (user clicks link to reset their own password)
Route::get('/password-reset/{token}/form', [ClientPasswordResetController::class, 'showResetForm'])->name('password-reset.reset');
Route::post('/password-reset/{token}/update', [ClientPasswordResetController::class, 'resetPassword'])->name('password-reset.update');

Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {

        Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

        Route::resource('categories', CategoryController::class);
        Route::resource('stocks', AdminStockController::class);
        Route::get('/stocks/generate-id/{categoryId}', [AdminStockController::class, 'generateId'])->name('stocks.generateId');
        Route::resource('inbound', InboundController::class);
        Route::resource('outbound', OutboundController::class);

        /*
        |--------------------------------------------------------------------------
        | PASSWORD RESET REQUESTS
        |--------------------------------------------------------------------------
        */
        Route::get('/password-reset', [AdminPasswordResetController::class, 'index'])->name('password-reset.index');
        Route::post('/password-reset/{id}/approve', [AdminPasswordResetController::class, 'approve'])->name('password-reset.approve');
        Route::post('/password-reset/{id}/reject', [AdminPasswordResetController::class, 'reject'])->name('password-reset.reject');

        // Admin-only token reset form and submission
        Route::get('/password-reset/{token}/form', [AdminPasswordResetController::class, 'showResetForm'])
            ->name('password-reset.admin.form');
        Route::post('/password-reset/reset', [AdminPasswordResetController::class, 'resetPassword'])
            ->name('password-reset.admin.reset');

        /*
        |--------------------------------------------------------------------------
        | REQUESTS WORKFLOW
        |--------------------------------------------------------------------------
        */

        // ✅ MAIN requests page (this gives route('requests.index'))
        Route::get('/requests', [AdminRequestController::class, 'index'])
            ->name('requests.index');

        // Admin notifications page
        Route::get('/notifications', [AdminDashboardController::class, 'notifications'])
            ->name('admin.notifications');

        // Notifications counts (AJAX)
        Route::get('/notifications/counts', [AdminDashboardController::class, 'counts'])
            ->name('admin.notifications.counts');

        // ✅ ALIAS name (so route('admin.requests') also works)
        Route::get('/requests-alias', function () {
            return redirect()->route('requests.index');
        })->name('admin.requests');

        // ✅ Buttons from your blade use this
        Route::put('/requests/{stockRequest}/decision', [AdminRequestController::class, 'decision'])
            ->name('admin.requests.decision');

        // ✅ Release -> Outbound
        Route::put('/requests/{stockRequest}/release', [AdminRequestController::class, 'release'])
            ->name('admin.requests.release');

        // Keep ONLY if your controller really has decide()
        Route::put('/requests/{stockRequest}/decide', [AdminRequestController::class, 'decide'])
            ->name('admin.requests.decide');

        /*
        |--------------------------------------------------------------------------
        | USER MANAGEMENT
        |--------------------------------------------------------------------------
        */
        Route::get('/users', [UserManagementController::class, 'index'])->name('admin.users.index');
        Route::get('/users/create', [UserManagementController::class, 'create'])->name('admin.users.create');
        Route::post('/users', [UserManagementController::class, 'store'])->name('admin.users.store');
        Route::get('/users/{id}/edit', [UserManagementController::class, 'edit'])->name('admin.users.edit');
        Route::put('/users/{id}', [UserManagementController::class, 'update'])->name('admin.users.update');
        Route::delete('/users/{id}', [UserManagementController::class, 'destroy'])->name('admin.users.destroy');
    });

/*
|--------------------------------------------------------------------------
| CLIENT ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('client')
    ->middleware(['auth', 'role:client'])
    ->group(function () {

        Route::get('/', [ClientDashboardController::class, 'index'])->name('client.dashboard');

        Route::get('/stocks', [ClientStockController::class, 'index'])->name('client.stocks');

        // ✅ Multi item request submit
        Route::post('/requests', [ClientRequestController::class, 'store'])->name('client.requests.store');

        // ✅ View requests
        Route::get('/requests', [ClientRequestController::class, 'index'])->name('client.requests');
        
        // ✅ Cancel pending request
        Route::post('/requests/{id}/cancel', [ClientRequestController::class, 'cancel'])->name('client.requests.cancel');

        // ✅ Account settings
        Route::get('/account', [AccountController::class, 'index'])->name('client.account');
        Route::post('/account/email', [AccountController::class, 'updateEmail'])->name('client.account.updateEmail');
        Route::post('/account/password', [AccountController::class, 'updatePassword'])->name('client.account.updatePassword');
    });
