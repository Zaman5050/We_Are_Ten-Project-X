<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TimesheetController;

Route::prefix("timesheet")->name("timesheet.")->group(function () { 
    Route::get('/',  [TimesheetController::class, 'index'])->name('index');
    Route::post('/log-time',  [TimesheetController::class, 'store'])->name('store');
    Route::put('/log-time',  [TimesheetController::class, 'update'])->name('update');
    Route::post('/update-timer-status',  [TimesheetController::class, 'updateTimerStatus'])->name('status.update.task');
    Route::delete('/{uuid}',  [TimesheetController::class, 'destroy'])->name('destroy');
    Route::post('/add-time-entry',  [TimesheetController::class, 'create'])->name('create');
});