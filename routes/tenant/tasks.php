<?php

use App\Http\Controllers\Tenant\TaskController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


Route::prefix("projects")->name("task.")->group(function () {

    Route::get('/{projectUuid}/tasks',  [TaskController::class, 'index'])->name('index');
});



Route::prefix("task")->name("task.")->group(function () {

    Route::get('/update-status/{taskUuid}/{statusUuid}',  [TaskController::class, 'updateStatus'])->name('updateStatus');
    Route::get('/mark-flag/{taskUuid}',  [TaskController::class, 'markFlag'])->name('markFlag');
    Route::get('/mark-archived/{taskUuid}/{status}',  [TaskController::class, 'markArchived'])->name('markArchived');
    Route::get('/delete/{taskUuid}',  [TaskController::class, 'deleteTask'])->name('delete');

    Route::get('/show/{taskUuid}',  [TaskController::class, 'show'])->name('show');

    Route::post('/store',  [TaskController::class, 'store'])->name('store');
    Route::post('/update-task-data/{taskUuid}',  [TaskController::class, 'updateTaskData'])->name('update-task-data');
    Route::put('/update/{taskUuid}',  [TaskController::class, 'update'])->name('update');

    Route::post('/upload', [TaskController::class, 'upload'])->name('upload');
    Route::delete('/attachment/{attachmentUuid}', [TaskController::class, 'deleteAttachment'])->name('delete.attachment');


});