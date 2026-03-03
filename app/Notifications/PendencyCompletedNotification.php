<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\SpecializedEducationalSupport\Pendency;

class PendencyCompletedNotification extends Notification
{
    use Queueable;

    public function __construct(private Pendency $pendency) {}

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Pendência concluída',
            'message' => "A pendência '{$this->pendency->title}' foi concluída.",
            'pendency_id' => $this->pendency->id,
            'url' => route('specialized-educational-support.pendencies.show', $this->pendency->id),
            'created_at' => now(),
        ];
    }
}