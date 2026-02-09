<?php

namespace App\Services\InclusiveRadar;

use App\Exceptions\InclusiveRadar\CannotDeleteLinkedBarrierException;
use App\Models\InclusiveRadar\Location;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LocationService
{
    public function store(array $data): Location
    {
        return DB::transaction(
            fn () => Location::create($data)
        );
    }

    public function update(Location $location, array $data): Location
    {
        return DB::transaction(function () use ($location, $data) {
            $location->update($data);

            return $location;
        });
    }

    public function toggleActive(Location $location): Location
    {
        return DB::transaction(function () use ($location) {
            $location->update([
                'is_active' => ! $location->is_active
            ]);

            return $location;
        });
    }

    public function delete(Location $location): void
    {
        DB::transaction(function () use ($location) {

            $hasActiveBarriers = $location
                ->barriers()
                ->whereNull('resolved_at')
                ->exists();

            if ($hasActiveBarriers) {
                throw new CannotDeleteLinkedBarrierException();
            }

            $location->delete();
        });
    }

}
