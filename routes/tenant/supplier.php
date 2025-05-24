<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupplierController;

Route::prefix("supplier")->name("supplier.")->group(function () { 
    Route::get('/',  [SupplierController::class, 'index'])->name('index');
    Route::post('/store',  [SupplierController::class, 'store'])->name('store');
    Route::post('/update/{supplierUuid}',  [SupplierController::class, 'update'])->name('update');
    Route::delete('/{uuid}',  [SupplierController::class, 'destroy'])->name('destroy');

});