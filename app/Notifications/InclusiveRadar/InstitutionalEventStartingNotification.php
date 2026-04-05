<?php

namespace App\Notifications\InclusiveRadar;

use App\Models\InclusiveRadar\InstitutionalEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InstitutionalEventStartingNotification extends Notification
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
            'title'      => 'Evento Iniciando Agora',
            'message'    => "O evento \"{$this->event->title}\" está começando agora! Local: {$this->event->location}.",
            'url'        => route('inclusive-radar.institutional-events.show', $this->event->id),
            'created_at' => now()->toDateTimeString(),
        ];
    }
}
