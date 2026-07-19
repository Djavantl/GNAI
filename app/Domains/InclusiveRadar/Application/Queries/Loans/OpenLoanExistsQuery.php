<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Queries\Loans;

use App\Domains\InclusiveRadar\Domain\Enums\LoanableType;
use App\Domains\InclusiveRadar\Domain\Models\Loan;
use Illuminate\Database\Eloquent\Builder;

final class OpenLoanExistsQuery
{
    public function execute(
        int $loanableId,
        LoanableType $loanableType,
        ?int $studentId,
        ?int $professionalId,
    ): bool {
        return Loan::query()
            ->where('loanable_id', $loanableId)
            ->where('loanable_type', $loanableType->value)
            ->whereNull('return_date')
            ->where(function (Builder $query) use ($studentId, $professionalId): void {
                if ($studentId !== null) {
                    $query->where('student_id', $studentId);

                    return;
                }

                $query->where('professional_id', $professionalId);
            })
            ->exists();
    }
}
