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
        $waitlist = $this->waitlist->loadMissing([
            'waitlistable',
            'student.person',
            'professional.person',
        ]);

        $itemName = $waitlist->waitlistable?->name ?? 'Recurso';

        $beneficiaryName = $waitlist->student?->person?->name
            ?? $waitlist->professional?->person?->name
            ?? 'Beneficiário desconhecido';

        $loanCreationParameters = array_filter([
            'item_id' => $waitlist->waitlistable_id,
            'item_type' => $waitlist->waitlistable_type,
            'student_id' => $waitlist->student_id,
            'professional_id' => $waitlist->professional_id,
        ], static fn (mixed $value): bool => $value !== null);

        return [
            'waitlist_id' => $waitlist->id,
            'title' => 'Próximo da fila disponível',
            'message' => "O item '{$itemName}' está disponível para o beneficiário: {$beneficiaryName}. Realize o empréstimo.",
            'url' => route('inclusive-radar.loans.create', $loanCreationParameters),
        ];
    }
}
