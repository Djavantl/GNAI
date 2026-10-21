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
            ->with(['locations:id,institution_id,name,type,latitude,longitude'])
            ->where(function ($query) use ($location): void {
                $query->where('is_active', true);

                if ($location !== null) {
                    $query->orWhere('id', $location->institution_id);
                }
            })
            ->orderBy('name')
            ->get();

        $selectedInstitution = null;

        if ($selectedInstitutionId !== null) {
            $selectedInstitution = $institutions->firstWhere('id', $selectedInstitutionId);
        } elseif ($location !== null) {
            $selectedInstitution = $institutions->firstWhere('id', $location->institution_id);
        }

        return [
            'institutions' => $institutions->map(fn (Institution $institution): array => [
                'value' => (string) $institution->id,
                'label' => $institution->name,
            ])->values(),
            'institutionsData' => $institutions->map(fn (Institution $institution): array => [
                'id' => $institution->id,
                'name' => $institution->name,
                'latitude' => (float) $institution->latitude,
                'longitude' => (float) $institution->longitude,
                'default_zoom' => $institution->default_zoom ?? Institution::DEFAULT_ZOOM,
                'locations' => $institution->locations->map(fn ($location): array => [
                    'id' => $location->id,
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                    'label' => trim($location->name.' ('.($location->type ?: 'Sem tipo').')'),
                ])->values(),
            ])->values(),
            'selectedInstitution' => $selectedInstitution,
        ];
    }
}
