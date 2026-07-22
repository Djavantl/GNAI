<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Queries\Locations;

use App\Domains\InclusiveRadar\Application\Data\Locations\ListLocationsData;
use App\Domains\InclusiveRadar\Domain\Models\Location;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final class ListLocationsQuery
{
    /**
     * @return LengthAwarePaginator<int, Location>
     */
    public function execute(ListLocationsData $filters): LengthAwarePaginator
    {
        $query = Location::query()
            ->with('institution');

        if (filled($filters->name)) {
            $query->where('name', 'like', '%'.trim($filters->name).'%');
        }

        if (filled($filters->institutionName)) {
            $query->whereHas('institution', function (Builder $query) use ($filters): void {
                $query->where('name', 'like', '%'.trim($filters->institutionName).'%');
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
