<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Queries\Loans;

use App\Domains\InclusiveRadar\Application\Data\Loans\ListLoansData;
use App\Domains\InclusiveRadar\Domain\Models\Loan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final class ListLoansQuery
{
    /**
     * @return LengthAwarePaginator<int, Loan>
     */
    public function execute(ListLoansData $filters): LengthAwarePaginator
    {
        $query = Loan::query()
            ->with([
                'loanable',
                'student:id,person_id,registration',
                'student.person:id,name',
                'professional:id,person_id,registration',
                'professional.person:id,name',
                'user:id,name',
            ]);

        if (filled($filters->student)) {
            $term = trim($filters->student);

            $query->whereHas(
                'student.person',
                static fn (Builder $query): Builder => $query->where('name', 'like', "%{$term}%"),
            );
        }

        if (filled($filters->professional)) {
            $term = trim($filters->professional);

            $query->whereHas(
                'professional.person',
                static fn (Builder $query): Builder => $query->where('name', 'like', "%{$term}%"),
            );
        }

        if (filled($filters->item)) {
            $term = trim($filters->item);

            $query->whereHasMorph(
                'loanable',
                ['*'],
                static fn (Builder $query): Builder => $query->where('name', 'like', "%{$term}%"),
            );
        }

        if ($filters->status !== null) {
            $query->where('status', $filters->status);
        }

        return $query
            ->orderByDesc('loan_date')
            ->orderByDesc('id')
            ->paginate($filters->perPage)
            ->withQueryString();
    }
}
