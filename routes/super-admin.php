<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Models\User;

Route::name("super-admin.")->middleware(['auth', 'role:' . USER::ROLE_SUPERADMIN])->group(function () {

    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

    Route::resource('/companies', CompanyController::class);
    Route::view('/create-admin/{company_uuid}', 'pages.companies.create-admin')->name('company.createCompanyAdmin');
    Route::post('/store-company-admin', [CompanyController::class, 'storeCompanyAdmin'])->name('company.storeCompanyAdmin');
    Route::get('/companies/toggle-status/{company_uuid}', [CompanyController::class, 'toggleCompanyStatus'])->name('company.toggleStatus');

    Route::post('/change-password/{user:uuid}', [CompanyController::class, 'changePassword'])->name('company.changePassword');
});
