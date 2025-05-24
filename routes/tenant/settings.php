<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\Tenant\SettingController;
use App\Http\Controllers\Tenant\ExchangeRateController;


Route::middleware(['auth', 'role:' . USER::ROLE_ADMIN])->name('settings.')->prefix('/settings')->group(function () {
    Route::get('/taxes', [SettingController::class, 'getTaxes'])->name('taxes.index');
    Route::resource('/exchange-rate', ExchangeRateController::class);
});
