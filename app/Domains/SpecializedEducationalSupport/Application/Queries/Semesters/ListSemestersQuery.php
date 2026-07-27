<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Semesters;

use App\Domains\SpecializedEducationalSupport\Application\Data\Semesters\ListSemestersData;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Semester;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ListSemestersQuery
{
    /**
     * @return LengthAwarePaginator<int, Semester>
     */
    public function execute(ListSemestersData $filters): LengthAwarePaginator
    {
        $query = Semester::query();

        if ($filters->year !== null) {
            $query->where('year', $filters->year);
        }

        if ($filters->term !== null) {
            $query->where('term', $filters->term);
        }

        if (filled($filters->label)) {
            $query->where('label', 'like', '%'.trim($filters->label).'%');
        }

        if ($filters->isCurrent !== null) {
            $query->where('is_current', $filters->isCurrent);
        }

        return $query
            ->orderByDesc('year')
            ->orderByDesc('term')
            ->paginate($filters->perPage)
            ->withQueryString();
    }
}
