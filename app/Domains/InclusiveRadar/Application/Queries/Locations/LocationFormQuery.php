<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Queries\Locations;

use App\Domains\InclusiveRadar\Domain\Models\Institution;
use App\Domains\InclusiveRadar\Domain\Models\Location;

final class LocationFormQuery
{
    /**
     * @return array<string, mixed>
     */
    public function execute(?Location $location = null, ?int $selectedInstitutionId = null): array
    {
        $institutions = Institution::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $selectedInstitution = null;

        if ($selectedInstitutionId !== null) {
            $selectedInstitution = $institutions->firstWhere('id', $selectedInstitutionId);
        } elseif ($location !== null) {
            $selectedInstitution = $institutions->firstWhere('id', $location->institution_id);
        }

        return [
            'institutions' => $institutions->pluck('name', 'id'),
            'institutionsData' => $institutions->mapWithKeys(fn (Institution $institution): array => [
                $institution->id => [
                    'latitude' => $institution->latitude,
                    'longitude' => $institution->longitude,
                    'default_zoom' => $institution->default_zoom ?? Institution::DEFAULT_ZOOM,
                ],
            ]),
            'selectedInstitution' => $selectedInstitution,
        ];
    }
}
