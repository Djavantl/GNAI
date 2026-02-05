<?php

namespace App\Services\InclusiveRadar;

use App\Enums\InclusiveRadar\BarrierStatus;
use App\Models\InclusiveRadar\Institution;
use App\Models\InclusiveRadar\Location;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LocationService
{
    public function listAll()
    {
        return Location::whereHas('institution', function($q){
            $q->whereNull('deleted_at');
        })
            ->with('institution')
            ->orderBy('name')
            ->get();
    }

    public function store(array $data): Location
    {
        return DB::transaction(function () use ($data) {
            $data['is_active'] = $data['is_active'] ?? true;
            return Location::create($data);
        });
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
            $location->update(['is_active' => ! $location->is_active]);
            return $location;
        });
    }

    public function delete(Location $location): void
    {

        $hasActiveBarriers = $location->barriers()
            ->whereNull('resolved_at')
            ->exists();

        if ($hasActiveBarriers) {
            throw ValidationException::withMessages([
                'delete' => 'Não é possível excluir o local: existem barreiras ativas vinculadas a ele.'
            ]);
        }

        DB::transaction(function () use ($location) {
            $location->delete();
        });
    }

    public function getActiveInstitutionsWithLocations()
    {
        return Institution::with(['locations' => function($query) {
            $query->where('is_active', true);
        }])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }
}
