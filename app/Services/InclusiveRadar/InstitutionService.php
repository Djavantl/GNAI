<?php

namespace App\Services\InclusiveRadar;

use App\Models\InclusiveRadar\Institution;
use Illuminate\Support\Facades\DB;

class InstitutionService
{
    public function listAll()
    {
        return Institution::with('locations')
            ->orderBy('name')
            ->get();
    }

    public function store(array $data): Institution
    {
        return DB::transaction(function () use ($data) {
            return Institution::create($data);
        });
    }

    public function update(Institution $institution, array $data): Institution
    {
        return DB::transaction(function () use ($institution, $data) {
            $institution->update($data);
            return $institution;
        });
    }

    public function toggleActive(Institution $institution): Institution
    {
        return DB::transaction(function () use ($institution) {
            $institution->update(['is_active' => ! $institution->is_active]);
            return $institution;
        });
    }

    public function delete(Institution $institution): void
    {
        DB::transaction(function () use ($institution) {
            $institution->delete();
        });
    }
}
