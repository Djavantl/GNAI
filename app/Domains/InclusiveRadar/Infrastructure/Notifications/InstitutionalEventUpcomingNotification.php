<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Infrastructure\Notifications;

use App\Domains\InclusiveRadar\Domain\Models\InstitutionalEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

final class InstitutionalEventUpcomingNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly InstitutionalEvent $event,
    ) {}

    /**
     * @return list<string>
     */
    public function via(mixed $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(mixed $notifiable): array
    {
        return [
            'event_id' => $this->event->id,
            'title' => 'Lembrete de Evento',
            'message' => "O evento \"{$this->event->title}\" acontece amanhã, {$this->event->start_date->format('d/m/Y')} às {$this->event->start_time->format('H:i')}. Local: {$this->event->location}.",
            'url' => route('inclusive-radar.institutional-events.show', $this->event->id, absolute: false),
        ];
    }
}
