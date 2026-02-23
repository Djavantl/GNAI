<?php

namespace App\Services\SpecializedEducationalSupport;

use App\Models\SpecializedEducationalSupport\Discipline;

class DisciplineService
{
    public function index(array $filters = [])
    {
        return Discipline::query()
            ->name($filters['name'] ?? null)
            ->active($filters['is_active'] ?? null)
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();
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
