<?php

use App\Domains\Backup\UI\Controllers\BackupController;
use Illuminate\Support\Facades\Route;

//Backups

Route::get('/backups', [BackupController::class, 'index'])
    ->name('backups.index')->middleware('can:backup.index');

Route::post('/backups/store', [BackupController::class, 'store'])
    ->name('backups.store')->middleware('can:backup.store');

Route::get('/backups/{backup}', [BackupController::class, 'show'])
    ->name('backups.show')->middleware('can:backup.show');

Route::get('/backups/{backup}/download', [BackupController::class, 'download'])
    ->name('backups.download')->middleware('can:backup.download');

Route::delete('/backups/{backup}', [BackupController::class, 'destroy'])
    ->name('backups.destroy')->middleware('can:backup.destroy');

Route::post('/backups/{backup}/restore', [BackupController::class, 'restore'])
    ->name('backups.restore')->middleware('can:backup.restore');

Route::post('/backups/upload', [BackupController::class, 'upload'])
    ->name('backups.upload')->middleware('can:backup.upload');
