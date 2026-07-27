<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Positions;

use App\Domains\SpecializedEducationalSupport\Application\Data\Positions\ListPositionsData;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Position;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ListPositionsQuery
{
    /**
     * @return LengthAwarePaginator<int, Position>
     */
    public function execute(ListPositionsData $filters): LengthAwarePaginator
    {
        $query = Position::query();

        if (filled($filters->name)) {
            $query->where('name', 'like', trim($filters->name).'%');
        }

        if (filled($filters->description)) {
            $query->where('description', 'like', '%'.trim($filters->description).'%');
        }

        if ($filters->isActive !== null) {
            $query->where('is_active', $filters->isActive);
        }

        return $query
            ->withCount('professionals')
            ->orderBy('name')
            ->paginate($filters->perPage)
            ->withQueryString();
    }
}
