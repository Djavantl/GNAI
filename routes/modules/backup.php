<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backup\BackupController;

//Backups

Route::get('/backups', [BackupController::class, 'index'])->name('backups.index');
Route::post('/backups/store', [BackupController::class, 'store'])->name('backups.store');
Route::get('/backups/{id}', [BackupController::class, 'show'])->name('backups.show');
Route::get('/backups/{id}/edit', [BackupController::class, 'edit'])->name('backups.edit');
Route::put('/backups/{id}', [BackupController::class, 'update'])->name('backups.update');
Route::get('/backups/{id}/download', [BackupController::class, 'download'])->name('backups.download');
Route::delete('/backups/{id}', [BackupController::class, 'destroy'])->name('backups.destroy');
