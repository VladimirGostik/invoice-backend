<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StreetController;
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
    Route::prefix('companies')->group(function () {
        // endpoints for MAIN companies
        Route::get('/main', [CompanyController::class, 'indexMain'])->name('companies.indexMain');
        Route::post('/main', [CompanyController::class, 'storeMain'])->name('companies.storeMain');

        // endpoints for RESIDENTIAL companies
        Route::get('/residential', [CompanyController::class, 'indexResidential'])->name('companies.indexResidential');
        Route::post('/residential', [CompanyController::class, 'storeResidential'])->name('companies.storeResidential');

        // Shared endpoints
        Route::get('/{company}', [CompanyController::class, 'show'])->name('companies.show');
        Route::put('/{company}', [CompanyController::class, 'update'])->name('companies.update');
        Route::delete('/{company}', [CompanyController::class, 'destroy'])->name('companies.destroy');
        Route::put('/{company}/customization', [CompanyController::class, 'updateCustomization'])->name('companies.customization.update');
    });

    Route::prefix('streets')->name('streets.')->group(function () {
        Route::get('/',            [StreetController::class, 'index'])->name('index');
        Route::post('/',           [StreetController::class, 'store'])->name('store');
        Route::get('/{street}',    [StreetController::class, 'show'])->name('show');
        Route::put('/{street}',    [StreetController::class, 'update'])->name('update');
        Route::delete('/{street}', [StreetController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('invoices')->group(function () {

        // Mesačné faktúry
        Route::prefix('monthly')->group(function () {
            Route::get('/', [InvoiceController::class, 'searchMonthly'])->name('invoices.searchMonthly');
            Route::post('/', [InvoiceController::class, 'storeMonthly'])->name('invoices.storeMonthly');
            Route::put('/{invoice}', [InvoiceController::class, 'updateMonthly'])->name('invoices.updateMonthly');
            Route::get('/{invoice}', [InvoiceController::class, 'viewMonthly'])->name('invoices.viewMonthly');
        });

        // Jednorazové faktúry
        Route::prefix('one-time')->group(function () {
            Route::get('/', [InvoiceController::class, 'searchOneTime'])->name('invoices.searchOneTime');
            Route::post('/', [InvoiceController::class, 'storeOneTime'])->name('invoices.storeOneTime');
            Route::put('/{invoice}', [InvoiceController::class, 'updateOneTime'])->name('invoices.updateOneTime');
            Route::post('/from-monthly', [InvoiceController::class, 'createOneTimeFromMonthly'])->name('invoices.createOneTimeFromMonthly');
        });
        
        // Všeobecné endpointy pre faktúry
        Route::get('/last-number/{company_id}/{billing_year}', [InvoiceController::class, 'getLastInvoiceNumber'])->name('invoices.last-number');
        Route::get('/{invoice}', [InvoiceController::class, 'view'])->name('invoices.view');
        Route::delete('/{invoice}', [InvoiceController::class, 'delete'])->name('invoices.delete');
    });

});
