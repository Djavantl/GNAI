<?php

namespace App\Notifications\InclusiveRadar;

use App\Models\InclusiveRadar\InstitutionalEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InstitutionalEventUpcomingNotification extends Notification
{
    use Queueable;

    public function __construct(private InstitutionalEvent $event) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'event_id'   => $this->event->id,
            'title'      => 'Lembrete de Evento',
            'message'    => "O evento \"{$this->event->title}\" acontece amanhã, {$this->event->start_date->format('d/m/Y')} às {$this->event->start_time->format('H:i')}. Local: {$this->event->location}.",
            'url'        => route('inclusive-radar.institutional-events.show', $this->event->id),
            'created_at' => now()->toDateTimeString(),
        ];
    }
}
