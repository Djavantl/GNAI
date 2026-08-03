<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Infrastructure\Notifications;

use App\Domains\SpecializedEducationalSupport\Domain\Models\Pendency;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

final class NewPendencyNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Pendency $pendency,
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
        $pendency = $this->pendency->loadMissing('creator');

        return [
            'pendency_id' => $pendency->id,
            'title' => $pendency->title,
            'message' => 'Você foi atribuído(a) a uma nova pendência.',
            'assigned_by' => $pendency->creator?->name,
            'priority' => $pendency->priority?->value,
            'url' => route('specialized-educational-support.pendencies.show', $pendency->id, absolute: false),
            'created_at' => $pendency->created_at?->toDateTimeString(),
        ];
    }

    public function toBroadcast(mixed $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }
}
