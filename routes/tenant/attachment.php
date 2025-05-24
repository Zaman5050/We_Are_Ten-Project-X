<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;

Route::post('/file/upload', [FileController::class, 'upload'])->name('files.upload');
Route::post('/file/delete', [FileController::class, 'delete'])->name('files.delete');

