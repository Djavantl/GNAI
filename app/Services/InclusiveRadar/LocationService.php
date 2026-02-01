<?php

namespace App\Services\InclusiveRadar;

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
        DB::transaction(function () use ($location) {

            $hasOpenBarriers = $location->barriers()
                ->whereHas('status', fn($q) => $q->where('name', '!=', 'Resolvida'))
                ->exists();

            if ($hasOpenBarriers) {
                throw ValidationException::withMessages([
                    'delete' => 'Não é possível excluir: existem barreiras associadas que ainda não estão resolvidas.'
                ]);
            }

            $location->delete();
        });
    }
}
