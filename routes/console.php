<?php

use App\Domains\Settings\Application\Queries\GetBackupSettingsQuery;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;

// Roda o backup nos horários definidos em Configurações > Backups (fuso da aplicação).
// A retenção é aplicada pelo próprio backup automático, conforme as mesmas configurações.
Schedule::command('backup:automatic')
    ->everyMinute()
    ->when(fn (GetBackupSettingsQuery $settings): bool => $settings->execute()->isScheduledAt(now()))
    ->description('Backup automático nos horários definidos em Configurações');

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
    ->withoutOverlapping(10)
    ->onFailure(function () {
        Log::error('Falha ao enviar lembretes de eventos institucionais.');
    });
