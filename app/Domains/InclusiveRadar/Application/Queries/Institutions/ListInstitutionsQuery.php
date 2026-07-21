<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Queries\Institutions;

use App\Domains\InclusiveRadar\Application\Data\Institutions\ListInstitutionsData;
use App\Domains\InclusiveRadar\Domain\Models\Institution;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ListInstitutionsQuery
{
    /**
     * @return LengthAwarePaginator<int, Institution>
     */
    public function execute(ListInstitutionsData $filters): LengthAwarePaginator
    {
        $query = Institution::query()
            ->with(['locations', 'barriers']);

        if (filled($filters->name)) {
            $query->where('name', 'like', '%'.trim($filters->name).'%');
        }

        if (filled($filters->location)) {
            $location = trim($filters->location);

            $query->where(function ($query) use ($location): void {
                $query
                    ->where('city', 'like', "%{$location}%")
                    ->orWhere('state', 'like', "%{$location}%");
            });
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
