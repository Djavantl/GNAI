<?php

namespace App\Services\SpecializedEducationalSupport;

use App\Models\SpecializedEducationalSupport\Person;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\SpecializedEducationalSupport\StudentContext;
use Illuminate\Support\Facades\DB;
use App\Services\SpecializedEducationalSupport\SemesterService;

class StudentContextService
{

    // mostrar todos contextos dos alunos

    public function index(Student $student)
    {
        return StudentContext::where('student_id', $student->id)->get();
    }

    // mostra contexto específico

    public function show(StudentContext $student_context)
    {
        return $student_context->load('student');
    }

    // mostra contexto atual

    public function showCurrent(Student $student)
    {
        return StudentContext::where('student_id', $student->id)
            ->where('is_current', true)
            ->with(['semester', 'evaluator'])
            ->first();
    }

    // Cria contexto

    public function create(Student $student, array $data): StudentContext
    {
        return DB::transaction(function () use ($student, $data) {

            $semester = app(SemesterService::class)->getCurrent();

            if (!$semester) {
                throw new \Exception('Não há semestre atual definido.');
            }

            // Se o novo contexto for atual, desativa apenas os outros do aluno
            if (!empty($data['is_current']) && $data['is_current']) {
                StudentContext::where('student_id', $student->id)
                    ->where('is_current', true)
                    ->update(['is_current' => false]);
            }

            $data['semester_id'] = $semester->id;
            return $student->contexts()->create($data);
        });
    }

    //  Atualiza Contexto

    public function update(StudentContext $student_context, array $data): StudentContext
    {
        DB::transaction(function () use ($student_context, $data) {
            $student_context->update($data);
        });

        return $student_context;
    }

    // deixa o contexto como atual

    public function setCurrent(StudentContext $student_context): StudentContext
    {
        return DB::transaction(function () use ($student_context) {

            StudentContext::where('student_id', $student_context->student_id)
                ->where('is_current', true)
                ->update(['is_current' => false]);

            $student_context->update(['is_current' => true]);

            return $student_context;
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
