<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backup\BackupController;

//Backups

Route::get('/backups', [BackupController::class, 'index'])
    ->name('backups.index')->middleware('can:backup.index');

Route::post('/backups/store', [BackupController::class, 'store'])
    ->name('backups.store')->middleware('can:backup.store');

Route::get('/backups/{id}', [BackupController::class, 'show'])
    ->name('backups.show')->middleware('can:backup.show');

Route::get('/backups/{id}/download', [BackupController::class, 'download'])
    ->name('backups.download')->middleware('can:backup.download');

Route::delete('/backups/{id}', [BackupController::class, 'destroy'])
    ->name('backups.destroy')->middleware('can:backup.destroy');

Route::post('/backups/{id}/restore', [BackupController::class, 'restore'])
    ->name('backups.restore')->middleware('can:backup.restore');

Route::post('/backups/upload', [BackupController::class, 'upload'])
    ->name('backups.upload')->middleware('can:backup.upload');
