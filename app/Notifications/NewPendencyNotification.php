<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\SpecializedEducationalSupport\Pendency;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NewPendencyNotification extends Notification
{
    use Queueable;

    protected Pendency $pendency;

    public function __construct(Pendency $pendency)
    {
        // armazenamos a pendência para usar nos canais
        $this->pendency = $pendency;
    }

    // quais canais? aqui usamos database e broadcast (broadcast é opcional)
    public function via($notifiable)
    {
        return ['database']; // troque para ['database', 'broadcast'] se for usar websockets
    }

    // dados que irão para a coluna data da tabela notifications
    public function toDatabase($notifiable)
    {
        return [
            'pendency_id' => $this->pendency->id,
            'title'       => $this->pendency->title,
            'message'     => "Você foi atribuído(a) a uma nova pendência.",
            'assigned_by' => $this->pendency->creator ? $this->pendency->creator->name : null,
            'priority'    => $this->pendency->priority->value ?? null,
            'url'         => route('specialized-educational-support.pendencies.show', $this->pendency->id),
            'created_at'  => $this->pendency->created_at->toDateTimeString(),
        ];
    }

    // se ativar broadcast, envie também com esse formato (opcional)
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }
}