<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserLeaveController;


Route::post('/apply-leave', [UserLeaveController::class, 'applyLeave'])->name('leave.apply');
Route::get('/leaves', [UserLeaveController::class, 'index'])->name('leaves.index');
Route::post('/leaves/{leave_uuid}/update-status', [UserLeaveController::class, 'updateStatus'])->name('leaves.updateStatus');
Route::get('/leaves/{uuid}/details', [UserLeaveController::class, 'getLeaveDetails'])->name('leaves.details');
Route::get('/notifications', [UserLeaveController::class, 'showNotifications']);
Route::get('/notifications', [UserLeaveController::class, 'index']);
Route::post('/notifications/mark-all-read', [UserLeaveController::class, 'markAllAsRead']);