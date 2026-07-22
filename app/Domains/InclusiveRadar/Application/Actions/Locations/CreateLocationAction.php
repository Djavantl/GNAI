<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Actions\Locations;

use App\Domains\InclusiveRadar\Application\Data\Locations\CreateLocationData;
use App\Domains\InclusiveRadar\Domain\DTOs\Locations\CreateLocationDTO;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidLocation;
use App\Domains\InclusiveRadar\Domain\Models\Location;

final readonly class CreateLocationAction
{
    /**
     * @throws InvalidLocation
     */
    public function execute(CreateLocationData $data): Location
    {
        $locationDTO = new CreateLocationDTO(
            institutionId: $data->institutionId,
            name: $data->name,
            latitude: $data->latitude,
            longitude: $data->longitude,
            type: $data->type,
            description: $data->description,
            googlePlaceId: $data->googlePlaceId,
            isActive: $data->isActive,
        );

        $location = Location::register($locationDTO);
        $location->save();

        return $location;
    }
}
