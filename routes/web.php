<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Tenant\DropboxController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::domain('{subdomain}.'.config('app.domain'))->name("tenant.")->group(function () {

    require __DIR__ . '/tenant/index.php';

});


Route::middleware("guest")->group(function () {

    Route::redirect('/', '/login')->name('home');

    Route::view('/login', 'pages.auth.super-admin.login')->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');

});
require __DIR__ . '/super-admin.php';

Route::get('dropbox-callback', [ DropboxController::class, 'handleDropboxCallback'])->name('tenant.user.dropbox.callback');