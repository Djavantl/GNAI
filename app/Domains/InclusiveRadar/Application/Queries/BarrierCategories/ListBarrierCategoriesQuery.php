<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Queries\BarrierCategories;

use App\Domains\InclusiveRadar\Application\Data\BarrierCategories\ListBarrierCategoriesData;
use App\Domains\InclusiveRadar\Domain\Models\BarrierCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ListBarrierCategoriesQuery
{
    /**
     * @return LengthAwarePaginator<int, BarrierCategory>
     */
    public function execute(ListBarrierCategoriesData $filters): LengthAwarePaginator
    {
        $query = BarrierCategory::query()
            ->withCount('barriers');

        if (filled($filters->name)) {
            $query->where('name', 'like', '%'.trim($filters->name).'%');
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
