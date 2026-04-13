<?php

namespace App\Services\SpecializedEducationalSupport;

use App\Models\SpecializedEducationalSupport\Discipline;
use DomainException;

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
        $newStatus = $data['is_active'] ?? $discipline->is_active;

        if ($discipline->is_active && !$newStatus) {

            if ($discipline->courses()->exists()) {
                throw new \DomainException(
                    "Não é possível inativar a disciplina '{$discipline->name}' pois ela está vinculada a um ou mais cursos."
                );
            }

            if ($discipline->teachers()->exists()) {
                throw new \DomainException(
                    "Não é possível inativar a disciplina '{$discipline->name}' pois ela está atribuída a professores."
                );
            }
        }

        $discipline->update([
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active'   => $newStatus,
        ]);

        return $discipline;
    }

    public function delete(Discipline $discipline): void
    {
        if ($discipline->teachers()->exists()) {
            throw new \DomainException(
                "Não é possível excluir a disciplina '{$discipline->name}' pois ela está vinculada a professores."
            );
        }

        if ($discipline->courses()->exists()) {
            throw new \DomainException(
                "Não é possível excluir a disciplina '{$discipline->name}' pois ela está vinculada a cursos."
            );
        }

        $discipline->delete();
    }
}
