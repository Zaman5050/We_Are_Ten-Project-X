<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tenant\ProjectController;
use App\Http\Controllers\Tenant\ProjectAreaController;
use App\Http\Controllers\Tenant\DropboxController;


Route::resource('projects', ProjectController::class);

Route::resource('/{project}/project-areas', ProjectAreaController::class)->except(['show', 'create', 'update']);

Route::get('/projects/{project}/details', [ProjectController::class, 'getDetails'])->name('projects.details');
Route::post('/projects/{project}/clone', [ProjectController::class, 'clone'])->name('projects.clone');
Route::get('/projects/{project}/files', [ProjectController::class, 'getFiles'])->name('projects.files');
Route::get('/projects/{project}/team', [ProjectController::class, 'getTeam'])->name('projects.team');
Route::post('/get-dropbox-authurl', [DropboxController::class, 'getDropboxAuthUrl'])->name('user.dropbox.getAuthUrl');
Route::post('/disconnect-dropbox', [DropboxController::class, 'disconnectDropbox'])->name('user.dropbox.disconnect');
Route::delete('/delete-from-dropbox', [DropboxController::class, 'deleteFromDropbox'])->name('dropbox.fileOrFolder.delete');


Route::post('/projects/upload', [ProjectController::class, 'upload'])->name('upload');
Route::delete('/projects/attachment/{attachmentUuid}', [ProjectController::class, 'deleteAttachment'])->name('delete.attachment');
Route::put('/projects/{project}/financial', [ProjectController::class, 'updateProjectFinancials'])->name('project.financials.update');
Route::get('/projects/{project}/financials-budget', [ProjectController::class, 'getProjectBudgetFinancial'])->name('project.financials.budget');
Route::get('/projects/{project}/financials-costing', [ProjectController::class, 'getProjectCostingFinancial'])->name('project.financials.costing');
Route::get('/projects/{project}/financials-procurement', [ProjectController::class, 'getProjectProcurementFinancial'])->name('project.financials.procurement');
Route::get('/projects/{project}/financials-services', [ProjectController::class, 'getProjectServiceFinancial'])->name('project.financials.services');
Route::post('/projects/{project}/create-dropbox-folder', [DropboxController::class, 'createFolder'])->name('project.dropbox.create-folder');
Route::post('/projects/{project}/upload-dropbox-files', [DropboxController::class, 'uploadFiles'])->name('project.dropbox.upload-files');
Route::post('/projects/{project}/share-dropbox-folder', [DropboxController::class, 'shareFolder'])->name('project.dropbox.share-folder');
