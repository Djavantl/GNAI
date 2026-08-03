<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Disciplines;

use App\Domains\SpecializedEducationalSupport\Application\Data\Disciplines\ListDisciplinesData;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Discipline;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ListDisciplinesQuery
{
    /**
     * @return LengthAwarePaginator<int, Discipline>
     */
    public function execute(ListDisciplinesData $filters): LengthAwarePaginator
    {
        $query = Discipline::query();

        if (filled($filters->name)) {
            $query->where('name', 'like', trim($filters->name).'%');
        }

        if ($filters->isActive !== null) {
            $query->where('is_active', $filters->isActive);
        }

        return $query
            ->orderBy('name')
            ->paginate($filters->perPage)
            ->withQueryString();
    }
}
