<?php

use App\Services\Backup\BackupService;
use Illuminate\Support\Facades\Schedule;

// Roda a limpeza às 12:00 (antes do backup novo)
Schedule::command('backup:clean')
    ->dailyAt('12:00')
    ->timezone('America/Bahia');

// Roda o backup às 12:05
Schedule::command('backup:run')
    ->dailyAt('12:05')
    ->timezone('America/Bahia')
    ->onSuccess(function () {
        app(BackupService::class)->sync();
    });
