<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\UI\Presenters;

use App\Domains\Inclusion\Domain\Exceptions\InvalidLoanableResource;
use App\Domains\Inclusion\Domain\Models\Loan;

final class LoanPresenter
{
    /**
     * @return array<string, mixed>
     *
     * @throws InvalidLoanableResource
     */
    public function loan(Loan $loan): array
    {
        $loan->loadMissing([
            'loanable',
            'student:id,person_id,registration',
            'student.person:id,name',
            'professional:id,person_id,registration',
            'professional.person:id,name',
            'user:id,name',
        ]);

        return [
            'id' => $loan->id,
            'loanable_id' => $loan->loanable_id,
            'item_type' => $loan->loanableType()->value,
            'item_type_label' => $loan->loanableType()->label(),
            'loanable_icon' => $loan->loanableType()->icon(),
            'loanable' => [
                'name' => $loan->loanable?->name ?? 'Item removido',
                'asset_code' => $loan->loanable?->asset_code,
            ],
            'student' => $loan->student ? [
                'name' => $loan->student->person?->name ?? 'Sem nome',
                'registration' => $loan->student->registration,
            ] : null,
            'professional' => $loan->professional ? [
                'name' => $loan->professional->person?->name ?? 'Sem nome',
                'registration' => $loan->professional->registration,
            ] : null,
            'user' => ['name' => $loan->user?->name ?? '---'],
            'loan_date' => $loan->loan_date?->format('Y-m-d\TH:i'),
            'loan_date_label' => $loan->loan_date?->format('d/m/Y H:i') ?? '---',
            'due_date' => $loan->due_date?->format('Y-m-d'),
            'due_date_label' => $loan->due_date?->format('d/m/Y') ?? '---',
            'return_date_label' => $loan->return_date?->format('d/m/Y H:i'),
            'status' => $loan->status?->value,
            'status_label' => $loan->statusLabel(),
            'status_color' => $loan->statusColor(),
            'observation' => $loan->observation,
            'is_active' => $loan->isActive(),
            'is_returned' => $loan->isReturned(),
            'is_overdue' => $loan->isOverdue(),
        ];
    }
}
