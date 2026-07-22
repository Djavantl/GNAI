<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Queries\Locations;

use App\Domains\InclusiveRadar\Domain\Models\Location;

final class ShowLocationQuery
{
    public function execute(Location $location): Location
    {
        return $location->load('institution');
    }
}
