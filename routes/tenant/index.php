<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Tenant\HomeController;


Route::redirect('/', '/login')->name('home');
Route::middleware(['guest'])->group(function () {
    Route::view('/login', 'pages.auth.login')->name('login');
    Route::post('login', [AuthController::class, 'tenantLogin'])->name('auth.login');
});


Route::view('forget-password', 'pages.auth.password.email-form')->name('password.email.show');
Route::view('forget-password/reset/{token}', 'pages.auth.password.reset')->name('password.reset');
Route::post('forget-password/email', [AuthController::class, 'sendResetLinkEmail'])->name('password.email.send');
Route::post('forget-password/reset', [AuthController::class, 'resetPassword'])->name('password.reset.put');

Route::get('/create-password/{token}',  [UserController::class, 'createPasswordView'])->name('create-password.view');
Route::post('/create-password/{token}',  [UserController::class, 'createPasswordStore'])->name('create-password.store');

Route::middleware(['auth', 'check_tenant_restriction'])->group(function () {

    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'tenantLogout'])->name('auth.logout');
    Route::get('/profile/{uuid}', [UserController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/{uuid}', [UserController::class, 'update'])->name('profile.update');
    Route::get('/member-profile/{uuid}', [UserController::class, 'editMemberProfile'])->name('member.profile.edit');
    Route::post('users/upload-profile-picture', [UserController::class, 'uploadProfilePicture'])->name('user.profile-picture.upload');
    Route::post('users/delete-profile-picture', [UserController::class, 'deleteProfilePicture'])->name('user.profile-picture.delete');
    Route::delete('/member-profile/{uuid}', [UserController::class, 'deleteMember'])->name('member.profile.delete');

    require __DIR__ . '/team.php';
    require __DIR__ . '/project.php';
    require __DIR__ . '/tasks.php';
    require __DIR__ . '/leave.php';
    require __DIR__ . '/timesheet.php';
    require __DIR__ . '/supplier.php';
    require __DIR__ . '/schedule.php';
    require __DIR__ . '/chat.php';
    require __DIR__ . '/procurement.php';
    require __DIR__ . '/attachment.php';
    require __DIR__ . '/category.php';
    require __DIR__ . '/settings.php';
});
