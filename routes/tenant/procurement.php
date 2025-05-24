<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tenant\ProcurementController;
use App\Http\Controllers\Tenant\ProductLibraryController;


Route::middleware(['can_procure'])->group(function () {
    Route::resource('/projects/{project}/procurements', ProcurementController::class)->names('projects.procurements');
    Route::post('/projects/{project}/add-procurement-from-library', [ProcurementController::class, 'addProcurementFromLibrary'])->name('projects.procurements.add-procurements-from-library');
    Route::get('/projects/{project}/export-procurements-to-excel', [ProcurementController::class, 'exportToExcel'])->name('projects.procurements.export-excel');
    Route::resource('/product-library', ProductLibraryController::class);
    Route::post('/projects/{project}/procurements/{procurement}/duplicate', [ProcurementController::class, 'duplicate'])->name('projects.procurements.duplicate');
    Route::get('/projects/{project}/procurements/{procurement}/status',    [ProcurementController::class, 'status'])->name('projects.procurements.status');
    Route::post('/procurements/{procurementUuid}/update-procurement-markup', [ProcurementController::class, 'updateProcurementMarkup'])->name('projects.procurements.updateMarkup');
});
