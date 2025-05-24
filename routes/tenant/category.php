<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;


Route::resource('categories', CategoryController::class);
Route::delete('/{uuid}',  [CategoryController::class, 'destroy'])->name('destroy');
