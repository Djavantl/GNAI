<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\Loans;

use App\Domains\Inclusion\Domain\Models\Item;
use App\Domains\Inclusion\Domain\Models\Loan;
use Illuminate\Database\Eloquent\Builder;

final class OpenLoanExistsQuery
{
    public function execute(
        int $loanableId,
        ?int $studentId,
        ?int $professionalId,
    ): bool {
        return Loan::query()
            ->where('loanable_id', $loanableId)
            ->where('loanable_type', (new Item)->getMorphClass())
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
