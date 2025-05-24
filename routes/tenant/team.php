<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::prefix("teams")->name("teams.")
->middleware(['role:admin'])
->controller(UserController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/{uuid}', 'show')->name('show');
    Route::get('/{uuid}/tasks', 'getTasks')->name('tasks');
    Route::get('/{uuid}/leaves', 'getLeaves')->name('leaves');
});
