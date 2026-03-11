<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backup\BackupController;

//Backups

Route::get('/backups', [BackupController::class, 'index'])->name('backups.index');
Route::post('/backups/store', [BackupController::class, 'store'])->name('backups.store');
Route::get('/backups/{id}', [BackupController::class, 'show'])->name('backups.show');
Route::get('/backups/{id}/download', [BackupController::class, 'download'])->name('backups.download');
Route::delete('/backups/{id}', [BackupController::class, 'destroy'])->name('backups.destroy');
Route::post('backups/{id}/restore', [BackupController::class, 'restore'])->name('backups.restore');
Route::post('/backups/upload', [BackupController::class, 'upload'])->name('backups.upload');
