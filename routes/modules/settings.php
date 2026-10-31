<?php

use App\Domains\Settings\UI\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

// Configurações

Route::get('/', [SettingsController::class, 'index'])
    ->name('index')->middleware('can:setting.view');

Route::get('/mail', [SettingsController::class, 'editMail'])
    ->name('mail.edit')->middleware('can:setting.view');

Route::put('/mail', [SettingsController::class, 'updateMail'])
    ->name('mail.update')->middleware('can:setting.update');

Route::post('/mail/test', [SettingsController::class, 'sendTestMail'])
    ->name('mail.test')->middleware('can:setting.update');

Route::get('/backups', [SettingsController::class, 'editBackup'])
    ->name('backup.edit')->middleware('can:setting.view');

Route::put('/backups', [SettingsController::class, 'updateBackup'])
    ->name('backup.update')->middleware('can:setting.update');
