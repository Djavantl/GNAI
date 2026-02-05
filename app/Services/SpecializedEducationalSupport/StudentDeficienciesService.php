<?php

namespace App\Services\SpecializedEducationalSupport;

use App\Models\SpecializedEducationalSupport\Student;
use App\Models\SpecializedEducationalSupport\StudentDeficiencies;
use Illuminate\Support\Facades\DB;

class StudentDeficienciesService
{
    // mostra contexto

    public function index(Student $student)
    {
        return StudentDeficiencies::where('student_id', $student->id)->get();
    }

    public function show(StudentDeficiencies $student_def)
    {
        return $student_def->load('deficiency');
    }

    // Cria contexto

    public function create(Student $student, array $data): StudentDeficiencies
    {
        return DB::transaction(function () use ($student, $data) {
        
            return $student->deficiencies()->create($data);
        });
    }

    //  Atualiza Contexto

    public function update(StudentDeficiencies $student_def, array $data): Bool
    {
        return DB::transaction(function () use ($student_def, $data) {

            return $student_def->update($data);
        });
    }

    // deleta contexto

    public function delete(StudentDeficiencies $student_def): void
    {
        DB::transaction(function () use ($student_def) {
            $student_def->delete();
        });
    }
}
