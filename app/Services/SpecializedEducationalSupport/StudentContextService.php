<?php

namespace App\Services\SpecializedEducationalSupport;

use App\Models\SpecializedEducationalSupport\Person;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\SpecializedEducationalSupport\StudentContext;
use Illuminate\Support\Facades\DB;
use App\Services\SpecializedEducationalSupport\SemesterService;
use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class StudentContextService
{

    // mostrar todos contextos dos alunos

    public function getByStudent(Student $student, array $filters = [])
    {
        return StudentContext::query()
            ->with(['semester'])
            ->where('student_id', $student->id)

            ->when($filters['semester_id'] ?? null, fn($q, $v) =>
                $q->where('semester_id', $v)
            )

            ->when(isset($filters['is_current']) && $filters['is_current'] !== '',
                fn($q) => $q->where('is_current', (bool) $filters['is_current'])
            )

            ->when($filters['evaluation_type'] ?? null, fn($q, $v) =>
                $q->where('evaluation_type', $v)
            )

            ->orderByDesc('version')
            ->paginate(10)
            ->withQueryString();
    }

    // mostra contexto específico

    public function show(StudentContext $student_context)
    {
        return $student_context->load('student');
    }

    // Cria contexto

    public function create(Student $student, array $data): StudentContext
    {
        return DB::transaction(function () use ($student, $data) {

            $exists = StudentContext::where('student_id', $student->id)->exists();

            if ($exists) {
                throw new \Exception('Este aluno já possui contexto. Crie uma nova versão.');
            }

            $semester = app(SemesterService::class)->getCurrent();

            if (!$semester) {
                throw new \Exception('Não há semestre atual definido.');
            }

            $data['semester_id'] = $semester->id;
            $data['version'] = 1;
            $data['is_current'] = true;
            $data['evaluation_type'] = 'initial';
            $data['evaluated_by_professional_id'] = $this->getAuthenticatedProfessionalId();

            return $student->contexts()->create($data);
        });
    }

    public function makeNewVersion(Student $student): StudentContext
    {
        $current = $this->showCurrent($student);

        if (!$current) {
            throw new \Exception('Não existe contexto atual.');
        }

        $lastVersion = StudentContext::where('student_id', $student->id)
            ->max('version');

        $newContext = $current->replicate();
        $newContext->version = $lastVersion + 1;
        $newContext->is_current = true;

        return $newContext;
    }

    public function createNewVersion(Student $student, array $data): StudentContext
    {
        return DB::transaction(function () use ($student, $data) {

            $current = $this->showCurrent($student);

            if (!$current) {
                throw new \Exception('Não existe contexto atual.');
            }

            $lastVersion = StudentContext::where('student_id', $student->id)
                ->lockForUpdate()
                ->max('version');

            $newContext = $current->replicate();
            $newContext->fill($data);
            $newContext->version = $lastVersion + 1;
            $newContext->is_current = true;
            $newContext->evaluation_type = 'periodic_review';
            $newContext->evaluated_by_professional_id = $this->getAuthenticatedProfessionalId();


            $this->removeCurrent($student);

            $newContext->save();

            return $newContext;
        });
    }

    //  Atualiza Contexto

    public function update(StudentContext $studentContext, array $data): StudentContext
    {
        return DB::transaction(function () use ($studentContext, $data) {

            if(!$studentContext->is_current){
                throw new \Exception('Não é possível editar um contexto que não é atual.');
            }

            $studentContext->update($data);

            return $studentContext;
        });
    }

    // deleta contexto

    public function delete(StudentContext $studentContext): void
    {
        DB::transaction(function () use ($studentContext) {

            $this->cancelVersion($studentContext);

            $studentContext->delete();
        });
    }

    // torna a ultima versão atual
    public function cancelVersion(StudentContext $studentContext)
    {
        if ($studentContext->is_current) {

            $previous = StudentContext::where('student_id', $studentContext->student_id)
            ->where('version', '<', $studentContext->version)
            ->orderByDesc('version')
            ->first();

            if ($previous) {
                $previous->update(['is_current' => true]);
            }
        }
    }

    // Criar nova versão atual identica a anterior

    public function restoreVersion(StudentContext $studentContext)
    {
        return DB::transaction(function () use ($studentContext) {

            $student = $studentContext->student;

            $lastVersion = StudentContext::where('student_id', $student->id)
                ->lockForUpdate()
                ->max('version');

            $newContext = $studentContext->replicate();
            $newContext->student_id = $student->id;
            $newContext->version = $lastVersion + 1;
            $newContext->is_current = true;
            $newContext->evaluation_type = 'periodic_review';
            $newContext->evaluated_by_professional_id = $this->getAuthenticatedProfessionalId();


            $this->removeCurrent($student);

            $newContext->save();

            return $newContext;
        });
    }

    // remove o contexto atual

    public function removeCurrent(Student $student)
    {
        StudentContext::where('student_id', $student->id)
            ->where('is_current', true)
            ->update(['is_current' => false]);
    }

    // mostra contexto atual

    public function showCurrent(Student $student): ?StudentContext
    {
        return StudentContext::where('student_id', $student->id)
            ->where('is_current', true)
            ->with(['semester', 'evaluator'])
            ->first();
    }

    protected function getAuthenticatedProfessionalId(): int
    {
        $user = Auth::user();

        // if (!$user || !$user->professional_id) {
        //     throw new \Exception('Usuário autenticado não possui profissional vinculado.');
        // }

        // return $user->professional_id;
        return $user->professional_id;
    }
}
