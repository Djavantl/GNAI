<?php

namespace App\Services\SpecializedEducationalSupport;

use App\Models\SpecializedEducationalSupport\Student;
use App\Models\SpecializedEducationalSupport\StudentDeficiencies;
use Illuminate\Support\Facades\DB;
use App\Models\SpecializedEducationalSupport\Deficiency;
use DomainException;

class StudentDeficienciesService
{
    // mostra contexto

    public function index(Student $student, array $filters = [])
    {
        return StudentDeficiencies::query()
            ->with('deficiency') 
            ->where('student_id', $student->id)
            ->deficiencyId($filters['deficiency_id'] ?? null)
            ->severity($filters['severity'] ?? null)
            ->paginate(10)
            ->withQueryString();
    }

    public function show(StudentDeficiencies $student_def)
    {
        return $student_def->load('deficiency');
    }

    // Cria contexto

    public function create(Student $student, array $data)
    {
        return DB::transaction(function () use ($student, $data) {
            $this->ensureDeficiencyIsActive($data['deficiency_id']);

            $student->deficiencies()->attach($data['deficiency_id'], [
                'severity' => $data['severity'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);
        });
    }

    //  Atualiza Contexto

    public function update(StudentDeficiencies $pivot, array $data)
    {
        return DB::transaction(function () use ($pivot, $data) {
            if (isset($data['deficiency_id'])) {
                $this->ensureDeficiencyIsActive($data['deficiency_id']);
            }

            return $pivot->update([
                'severity' => $data['severity'],
                'notes' => $data['notes'],
            ]);
        });
    }

    // deleta contexto

    public function delete(StudentDeficiencies $pivot)
    {
        DB::transaction(fn() => $pivot->delete());
    }

    private function ensureDeficiencyIsActive(int $deficiencyId): void
    {
        $deficiency = Deficiency::findOrFail($deficiencyId);

        $deficiency->ensureIsActive();
    }
}
