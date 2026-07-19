<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\UI\Presenters;

use App\Domains\InclusiveRadar\Domain\Enums\LoanableType;
use App\Domains\InclusiveRadar\Domain\Enums\LoanStatus;
use App\Domains\InclusiveRadar\Domain\Models\Loan;

final readonly class LoanPresenter
{
    public static function isOverdue(Loan $loan): bool
    {
        return $loan->status === LoanStatus::ACTIVE
            && $loan->due_date !== null
            && $loan->due_date->isPast();
    }

    public static function isActive(Loan $loan): bool
    {
        return $loan->status === LoanStatus::ACTIVE;
    }

    public static function isReturned(Loan $loan): bool
    {
        return $loan->return_date !== null
            || $loan->status?->isReturned() === true;
    }

    public static function statusLabel(Loan $loan): string
    {
        return self::isOverdue($loan)
            ? 'Em Atraso'
            : ($loan->status?->label() ?? 'Status desconhecido');
    }

    public static function statusColor(Loan $loan): string
    {
        if (self::isOverdue($loan)) {
            return 'danger';
        }

        return match ($loan->status) {
            LoanStatus::ACTIVE => 'success',
            LoanStatus::RETURNED => 'primary',
            LoanStatus::LATE => 'warning',
            LoanStatus::DAMAGED => 'danger',
            default => 'secondary',
        };
    }

    public static function loanableTypeLabel(Loan $loan): string
    {
        return match (LoanableType::tryFrom($loan->loanable_type)) {
            LoanableType::AssistiveTechnology => 'Tecnologia Assistiva',
            LoanableType::AccessibleEducationalMaterial => 'Material Pedagógico Acessível',
            default => 'Recurso',
        };
    }

    public static function loanableIcon(Loan $loan): string
    {
        return match (LoanableType::tryFrom($loan->loanable_type)) {
            LoanableType::AssistiveTechnology => 'fa-microchip',
            LoanableType::AccessibleEducationalMaterial => 'fa-book',
            default => 'fa-box',
        };
    }
}
