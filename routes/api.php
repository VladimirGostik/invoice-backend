<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ResidentialCompanyController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;

use Illuminate\Support\Facades\Route;

Route::prefix('auth')->name('auth.')->group(function () {

    // Get a JWT via given credentials
    Route::post('login', [AuthController::class, 'login'])->name('login');

    // Send a password reset link
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');

    // Reset the password
    Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('reset-password');
});

Route::group(['middleware' => ['jwt.auth', 'token.validation']], function () {

    /*
    |--------------------------------------------------------------------------
    | Authentication routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('auth')->name('auth.')->group(function () {

        // Refresh a token
        Route::post('refresh', [AuthController::class, 'refresh'])->name('refresh');

        // Set entity for the token
        //    Route::post('set-entity', [AuthController::class, 'setEntity'])->name('set-entity');

        // Get the authenticated user
        Route::post('user', [AuthController::class, 'user'])->name('user');

        // Log the user out (Invalidate the token)
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    });

    Route::prefix('users')->name('users.')->group(function () {

        // Check if a user with specified email exists
        Route::post('email-exists', [UserController::class, 'emailExists'])->name('email-exists');
        // Change the password of the specified user
        Route::post('{user}/change-password', [UserController::class, 'changePassword'])->name('change-password');
    });

    Route::apiResource('users', UserController::class);
    Route::apiResource('roles', RoleController::class)->only('index');

    // Companies resource
    Route::prefix('companies')->name('companies.')->group(function () {
        Route::get('/',            [CompanyController::class, 'index'])->name('index');
        Route::post('/',           [CompanyController::class, 'store'])->name('store');
        Route::get('/{company}',   [CompanyController::class, 'show'])->name('show');
        Route::put('/{company}',   [CompanyController::class, 'update'])->name('update');
        Route::delete('/{company}',[CompanyController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('residential-companies')->name('residential-companies.')->group(function () {
        Route::get('/',            [ResidentialCompanyController::class, 'index'])->name('index');
        Route::post('/',           [ResidentialCompanyController::class, 'store'])->name('store');
        Route::get('/{residentialCompany}', [ResidentialCompanyController::class, 'show'])->name('show');
        Route::put('/{residentialCompany}', [ResidentialCompanyController::class, 'update'])->name('update');
        Route::delete('/{residentialCompany}', [ResidentialCompanyController::class, 'destroy'])->name('destroy');
    });

});
