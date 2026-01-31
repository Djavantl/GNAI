<?php

namespace App\Services\SpecializedEducationalSupport;

use App\Models\SpecializedEducationalSupport\Person;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\SpecializedEducationalSupport\StudentContext;
use Illuminate\Support\Facades\DB;

class StudentContextService
{
    // mostra contexto

    public function show(Student $student)
    {
        return StudentContext::where('student_id', $student->id)->first();
    }

    // Cria contexto

    public function create(Student $student, array $data): StudentContext
    {
        return DB::transaction(function () use ($student, $data) {
        
            return $student->context()->create($data);
        });
    }

    //  Atualiza Contexto

    public function update(StudentContext $student_context, array $data): Bool
    {
        return DB::transaction(function () use ($student_context, $data) {

            return $student_context->update($data);
        });
    }

    // deleta contexto

    public function delete(StudentContext $student_context): void
    {
        DB::transaction(function () use ($student_context) {
            $student_context->delete();
        });
    }
}
