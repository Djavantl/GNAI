<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Infrastructure\Notifications;

use App\Domains\InclusiveRadar\Domain\Models\Waitlist;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

final class LoanableItemAvailableNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Waitlist $waitlist,
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
        $itemName = $this->waitlist->waitlistable->name ?? 'Recurso';

        $beneficiaryName = $this->waitlist->student?->person?->name
            ?? $this->waitlist->professional?->person?->name
            ?? 'Beneficiário desconhecido';

        return [
            'waitlist_id' => $this->waitlist->id,
            'title' => 'Próximo da fila disponível',
            'message' => "O item '{$itemName}' está disponível para o beneficiário: {$beneficiaryName}. Realize o empréstimo.",
            'url' => route('inclusive-radar.loans.create', [
                'item_id' => $this->waitlist->waitlistable_id,
                'item_type' => $this->waitlist->waitlistable_type,
                'student_id' => $this->waitlist->student_id,
                'professional_id' => $this->waitlist->professional_id,
            ]),
            'created_at' => now()->toDateTimeString(),
        ];
    }
}
