<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Infrastructure\Notifications;

use App\Domains\InclusiveRadar\Domain\Models\Loan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

final class LoanOverdueNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Loan $loan,
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
        $loan = $this->loan->loadMissing([
            'loanable',
            'student.person',
            'professional.person',
        ]);

        $beneficiary = $loan->student?->person?->name
            ?? $loan->professional?->person?->name
            ?? 'N/A';

        $itemName = $loan->loanable?->name ?? 'Item';
        $daysOverdue = abs((int) ceil(now()->diffInDays($loan->due_date, false)));

        return [
            'loan_id' => $loan->id,
            'title' => 'Empréstimo Atrasado',
            'message' => "O item '{$itemName}' está com o beneficiário {$beneficiary} e encontra-se atrasado há {$daysOverdue} dia(s).",
            'url' => route('inclusive-radar.loans.show', $loan->id),
        ];
    }
}
