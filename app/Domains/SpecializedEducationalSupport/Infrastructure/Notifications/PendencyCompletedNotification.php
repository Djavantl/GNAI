<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Infrastructure\Notifications;

use App\Domains\SpecializedEducationalSupport\Domain\Models\Pendency;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

final class PendencyCompletedNotification extends Notification
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
        return [
            'title' => 'Pendência concluída',
            'message' => "A pendência '{$this->pendency->title}' foi concluída.",
            'pendency_id' => $this->pendency->id,
            'url' => route('specialized-educational-support.pendencies.show', $this->pendency->id),
            'created_at' => now(),
        ];
    }
}
