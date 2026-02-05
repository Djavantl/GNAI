<?php

namespace App\Services\SpecializedEducationalSupport;

use App\Models\SpecializedEducationalSupport\Semester;
use Illuminate\Support\Facades\DB;

class SemesterService
{
    /**
     * Listar todos os semestres
     */
    public function index()
    {
        return Semester::orderBy('year', 'desc')
            ->orderBy('term', 'desc')
            ->get();
    }

    /**
     * Criar semestre
     */
    public function create(array $data): Semester
    {
        return DB::transaction(function () use ($data) {

            // Se criar já como atual, desativa os outros
            if (!empty($data['is_current']) && $data['is_current']) {
                Semester::where('is_current', true)
                    ->update(['is_current' => false]);
            }

            return Semester::create($data);
        });
    }

    /**
     * Atualizar semestre
     */
    public function update(Semester $semester, array $data): Semester
    {
        return DB::transaction(function () use ($semester, $data) {

            if (!empty($data['is_current']) && $data['is_current']) {
                Semester::where('is_current', true)
                    ->where('id', '!=', $semester->id)
                    ->update(['is_current' => false]);
            }

            $semester->update($data);

            return $semester;
        });
    }

    /**
     * Definir semestre como atual
     */
    public function setCurrent(Semester $semester): Semester
    {
        return DB::transaction(function () use ($semester) {

            Semester::where('is_current', true)
                ->update(['is_current' => false]);

            $semester->update(['is_current' => true]);

            return $semester;
        });
    }

    /**
     * Obter semestre atual
     */
    public function getCurrent(): ?Semester
    {
        return Semester::where('is_current', true)->first();
    }

    /**
     * Remover semestre
     */
    public function delete(Semester $semester): void
    {
        $semester->delete();
    }
}
