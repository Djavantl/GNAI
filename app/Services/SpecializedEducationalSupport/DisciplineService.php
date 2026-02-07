<?php

namespace App\Services\SpecializedEducationalSupport;

use App\Models\SpecializedEducationalSupport\Discipline;

class DisciplineService
{
    public function index()
    {
        return Discipline::orderBy('name', 'asc')->get();
    }

    public function show(Discipline $discipline)
    {
        return $discipline;
    }

    public function create(array $data): Discipline
    {
        return Discipline::create([
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active'   => $data['is_active'] ?? true,
        ]);
    }

    public function update(Discipline $discipline, array $data): Discipline
    {
        $discipline->update([
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active'   => $data['is_active'] ?? $discipline->is_active,
        ]);

        return $discipline;
    }

    public function delete(Discipline $discipline): void
    {
        $discipline->delete();
    }
}
