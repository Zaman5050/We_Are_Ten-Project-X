<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tenant\ScheduleController;
use App\Http\Controllers\Tenant\MaterialLibraryController;


Route::resource('/projects/{project}/schedules', ScheduleController::class )->names('projects.schedules');
Route::post('/projects/{project}/add-schedules-from-library', [ScheduleController::class, 'addScheduleFromLibrary' ] )->name('projects.schedules.add-schedules-from-library');
Route::get('/projects/{project}/export-schedules-to-excel', [ScheduleController::class, 'exportToExcel' ] )->name('projects.schedules.export-excel');
Route::resource('/material-library', MaterialLibraryController::class);