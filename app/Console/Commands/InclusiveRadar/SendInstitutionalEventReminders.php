<?php

namespace App\Console\Commands\InclusiveRadar;

use App\Domains\InclusiveRadar\Domain\Models\InstitutionalEvent;
use App\Domains\InclusiveRadar\Infrastructure\Notifications\InstitutionalEventStartingNotification;
use App\Domains\InclusiveRadar\Infrastructure\Notifications\InstitutionalEventUpcomingNotification;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

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

        foreach ($events as $event) {
            $result = $this->notifyUsersForEvent($event, InstitutionalEventUpcomingNotification::class);

            if ($result['total'] === 0) {
                $this->info("[Amanhã] Nenhum usuário para notificar: {$event->title}");
                continue;
            }

            if ($result['sent'] === 0 && $result['failed'] === 0) {
                $this->info("[Amanhã] Já notificado: {$event->title}");
                continue;
            }

            if ($result['sent'] === 0) {
                $this->error("[Amanhã] Falha ao enviar lembretes: {$event->title}");
                continue;
            }

            $this->info("[Amanhã] Lembretes enviados: {$event->title}");
        }
    }

    private function sendStartingReminders(): void
    {
        $now = now();
        $windowStart = $now->copy()->subMinutes(5);
        $windowStartTime = $windowStart->isSameDay($now)
            ? $windowStart->format('H:i:00')
            : '00:00:00';

        $events = InstitutionalEvent::where('is_active', true)
            ->whereDate('start_date', $now->toDateString())
            ->whereTime('start_time', '>=', $windowStartTime)
            ->whereTime('start_time', '<=', $now->format('H:i:59'))
            ->get();

        if ($events->isEmpty()) {
            $this->info('Nenhum evento iniciando agora.');
            return;
        }

        foreach ($events as $event) {
            $result = $this->notifyUsersForEvent($event, InstitutionalEventStartingNotification::class);

            if ($result['total'] === 0) {
                $this->info("[Iniciando] Nenhum usuário para notificar: {$event->title}");
                continue;
            }

            if ($result['sent'] === 0 && $result['failed'] === 0) {
                $this->info("[Iniciando] Já notificado: {$event->title}");
                continue;
            }

            if ($result['sent'] === 0) {
                $this->error("[Iniciando] Falha ao enviar notificações: {$event->title}");
                continue;
            }

            $this->info("[Iniciando] Notificações enviadas: {$event->title}");
        }
    }

    /**
     * @param class-string $notificationClass
     * @return array{total: int, sent: int, skipped: int, failed: int}
     */
    private function notifyUsersForEvent(InstitutionalEvent $event, string $notificationClass): array
    {
        $result = [
            'total' => 0,
            'sent' => 0,
            'skipped' => 0,
            'failed' => 0,
        ];

        $alreadyNotifiedIds = DB::table('notifications')
            ->where('notifiable_type', (new User())->getMorphClass())
            ->where('type', $notificationClass)
            ->where('data->event_id', $event->id)
            ->pluck('notifiable_id')
            ->flip();

        User::query()
            ->select(['id'])
            ->chunkById(100, function ($users) use ($event, $notificationClass, $alreadyNotifiedIds, &$result): void {
                foreach ($users as $user) {
                    $result['total']++;

                    if ($alreadyNotifiedIds->has($user->id)) {
                        $result['skipped']++;
                        continue;
                    }

                    try {
                        $user->notify(new $notificationClass($event));
                        $result['sent']++;
                    } catch (Throwable $exception) {
                        $result['failed']++;

                        Log::error('Falha ao enviar lembrete de evento institucional.', [
                            'event_id' => $event->id,
                            'user_id' => $user->id,
                            'notification' => $notificationClass,
                            'exception' => $exception,
                        ]);
                    }
                }
            });

        return $result;
    }
}
