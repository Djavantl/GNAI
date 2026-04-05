<?php

use App\Services\Backup\BackupService;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;

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

// Roda verificacao de emprestimos em atraso
Schedule::command('loans:check-overdue')
    ->dailyAt('12:05')
    ->timezone('America/Bahia')
    ->onFailure(function () {
        Log::error('Falha ao processar verificação de empréstimos em atraso.');
    });

// Roda lembretes de eventos institucionais (1 dia antes + iniciando agora)
Schedule::command('inclusive-radar:send-event-reminders')
    ->everyMinute()
    ->timezone('America/Bahia')
    ->onFailure(function () {
        Log::error('Falha ao enviar lembretes de eventos institucionais.');
    });
