<?php

namespace App\Console\Commands\InclusiveRadar;

use App\Models\InclusiveRadar\InstitutionalEvent;
use App\Models\User;
use App\Notifications\InclusiveRadar\InstitutionalEventUpcomingNotification;
use App\Notifications\InclusiveRadar\InstitutionalEventStartingNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendInstitutionalEventReminders extends Command
{
    protected $signature = 'inclusive-radar:send-event-reminders';
    protected $description = 'Envia lembretes de eventos institucionais (1 dia antes e no início)';

    public function handle(): void
    {
        $this->sendUpcomingReminders();
        $this->sendStartingReminders();
    }

    private function sendUpcomingReminders(): void
    {
        $tomorrow = now()->addDay()->toDateString();

        $events = InstitutionalEvent::where('is_active', true)
            ->whereDate('start_date', $tomorrow)
            ->get();

        if ($events->isEmpty()) {
            $this->info('Nenhum evento amanhã.');
            return;
        }

        $users = User::all();

        foreach ($events as $event) {
            $alreadySent = DB::table('notifications')
                ->where('type', InstitutionalEventUpcomingNotification::class)
                ->where('data->event_id', $event->id)
                ->exists();

            if ($alreadySent) {
                $this->info("[Amanhã] Já notificado: {$event->title}");
                continue;
            }

            $users->each->notify(new InstitutionalEventUpcomingNotification($event));
            $this->info("[Amanhã] Lembretes enviados: {$event->title}");
        }
    }

    private function sendStartingReminders(): void
    {
        $now = now();

        $events = InstitutionalEvent::where('is_active', true)
            ->whereDate('start_date', $now->toDateString())
            ->whereTime('start_time', '>=', $now->format('H:i:00'))
            ->whereTime('start_time', '<=', $now->format('H:i:59'))
            ->get();

        if ($events->isEmpty()) {
            $this->info('Nenhum evento iniciando agora.');
            return;
        }

        $users = User::all();

        foreach ($events as $event) {
            $alreadySent = DB::table('notifications')
                ->where('type', InstitutionalEventStartingNotification::class)
                ->where('data->event_id', $event->id)
                ->exists();

            if ($alreadySent) {
                $this->info("[Iniciando] Já notificado: {$event->title}");
                continue;
            }

            $users->each->notify(new InstitutionalEventStartingNotification($event));
            $this->info("[Iniciando] Notificações enviadas: {$event->title}");
        }
    }
}
