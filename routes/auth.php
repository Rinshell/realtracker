<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UsersController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\PermissionsController;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TechnoController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\EventController;

use Filament\FilamentManager;

Route::group(['middleware' => ['auth','permission:reading']], function () {
    Route::get('/products', [ProductController::class, 'index'])->name('products');
    Route::get('/product/{id}', [ProductController::class, 'getProduct'])->name('product');
});

Route::group(['middleware' => ['permission:edit event']], function () {
    Route::resource('events', EventController::class)->only([
        'edit', 'create', 'update', 'store', 'destroy'
    ]);
});

/*
Route::group(['middleware' => ['permission:edit users']], function () {
    Route::resource('users', UsersController::class);
    Route::resource('roles', RolesController::class);
    Route::resource('permissions', PermissionsController::class);
});

Route::group(['middleware' => ['permission:edit product']], function () {
    Route::resource('technos', TechnoController::class)->only([
        'edit', 'create', 'update', 'store', 'destroy'
    ]);
    
    Route::resource('status', StatusController::class)->only([
        'edit', 'create', 'update', 'store', 'destroy'
    ]);
    
    Route::resource('products', ProductController::class);
});


Route::group(['middleware' => ['permission:edit techno']], function () {
    Route::get('/service', [ServiceController::class, 'index'])->name('service');
});
*/
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
                ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);
/*

    Route::get('register', [RegisteredUserController::class, 'create'])
                ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
                ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
                ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
                ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
                ->name('password.store');*/
});

Route::group(['middleware' => ['auth']], function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');
});

/*

Route::group(['middleware' => ['auth', 'permission']], function () {
    Route::get('verify-email', [EmailVerificationPromptController::class, '__invoke'])
                ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
                ->middleware(['signed', 'throttle:6,1'])
                ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
                ->middleware('throttle:6,1')
                ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
                ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

});
*/