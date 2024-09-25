<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;

Route::get('/', [DocumentController::class, 'index']);
Route::post('/upload', [DocumentController::class, 'upload'])->name('upload');
Route::get('/edit/{id}', [DocumentController::class, 'edit'])->name('edit');
Route::post('/update/{id}', [DocumentController::class, 'update'])->name('update');
Route::get('/download/{id}', [DocumentController::class, 'download'])->name('download');
Route::get('/download-pdf/{id}', [DocumentController::class, 'downloadPdf'])->name('download-pdf');
Route::get('/testpdf',[DocumentController::class, 'htmlToPdf'])->name('testpdf');