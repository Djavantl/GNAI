<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Queries\Locations;

use App\Domains\InclusiveRadar\Domain\Models\Location;

final class LocationHasUnresolvedBarriersQuery
{
    public function execute(Location $location): bool
    {
        return $location
            ->barriers()
            ->whereNull('resolved_at')
            ->exists();
    }
}
