<?php

namespace App\Services\SpecializedEducationalSupport;

use App\Models\SpecializedEducationalSupport\Pei;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\SpecializedEducationalSupport\SpecificObjective;
use App\Models\SpecializedEducationalSupport\ContentProgrammatic;
use App\Models\SpecializedEducationalSupport\Methodology;
use Illuminate\Support\Facades\DB;
use App\Services\SpecializedEducationalSupport\SemesterService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth; 
use App\Models\User;

class PeiService
{
    protected $semesterService;

    public function __construct(SemesterService $semesterService)
    {
        $this->semesterService = $semesterService;
    }


    public function all(array $filters = [])
    {
        $user = auth()->user();

        $query = Pei::query()
            ->with([
                'student.person',
                'semester',
            ]);

        return $query
            ->student($filters['student_id'] ?? null)
            ->semester($filters['semester_id'] ?? null)
            ->finished($filters['is_finished'] ?? null)
            ->version($filters['version'] ?? null)
            ->latest()
            ->paginate(10)
            ->withQueryString();
    }

    /**
     * Lista todos os PEIs de um estudante específico.
     */
    public function index(Student $student, array $filters = [])
    {
        return Pei::query()
            ->where('student_id', $student->id)
            ->visibleToUser(auth()->user())
            ->with(['student.person', 'semester'])
            ->semester($filters['semester_id'] ?? null)
            ->finished($filters['is_finished'] ?? null)
            ->version($filters['version'] ?? null)
            ->latest()
            ->paginate(10)
            ->withQueryString();
    }

    /**
     * Cria a estrutura inicial do PEI vinculada ao contexto atual do aluno.
     */
    public function create(Student $student): Pei
    {
        return DB::transaction(function () use ($student) {

            $exists = Pei::where('student_id', $student->id)->exists();

            if ($exists) {
                throw new \Exception('Este aluno já possui um Pei. Crie uma nova versão.');
            }

            $semesterId = $this->semesterService->getCurrent()?->id;
            if (!$semesterId) {
                throw new \Exception('O sistema não possui semestre atual cadastrado');
            }

            $studentCourse = $student->currentCourse()->first();
            if (!$studentCourse) {
                throw new \Exception('Este aluno não possui matrícula vigente');
            }
            $course = $studentCourse->course;

            $currentContext = $student->contexts()->where('is_current', true)->first();
             if (!$currentContext) {
                throw new \Exception('Este aluno não possui um contexto atual');
            }


            $data['student_id'] = $student->id;
            $data['creator_id'] = Auth::id();
            $data['semester_id'] = $semesterId;
            $data['course_id'] = $course->id;
            $data['student_context_id'] = $currentContext->id;
            $data['is_finished'] = false;
            $data['version'] = 1;
            $data['is_current'] = true;

        
            return Pei::create($data);
        });
    }

    public function createVersion(Student $student): Pei
    {
        return DB::transaction(function () use ($student) {
            $user = auth()->user();

            $pei =  $this->getCurrent($student);

            if (!$pei) {
                throw new \DomainException('Aluno não possui PEI atual');
            }

            if (!$pei->is_finished) {
                throw new \Exception('Só é possível criar nova versão a partir de um PEI atual finalizado.');
            }

            $semesterId = $this->semesterService->getCurrent()?->id;

            if (!$semesterId) {
                throw new \DomainException('O sistema não possui semestre atual cadastrado');
            }

            $student = $pei->student;
            $studentContext = $student->currentContext()->first();

            if (!$studentContext) {
                throw new \Exception('Este aluno não possui um Contexto atual definido.');
            }

            $this->removeCurrent($pei);

            $new = Pei::create([
                'creator_id'         => $user->id,
                'student_id'         => $pei->student_id,
                'semester_id'        => $semesterId,
                'course_id'          => $pei->course_id,
                'student_context_id' => $studentContext->id,
                'is_finished'        => false,
                'version'            => ($pei->version ?? 1) + 1,
                'is_current'         => true,
            ]);

            // Copiar pei_disciplines
            foreach ($pei->peiDisciplines as $obj) {
                $new->peiDisciplines()->create($obj->replicate()->toArray());
            }

            return $new;
        });
    }

    /**
     * Finaliza o PEI, impedindo novas edições se necessário.
     */
    public function finish(Pei $pei): Pei
    {
        if ($pei->creator_id !== auth()->id()) {
            throw new \Exception('Acesso negado: apenas o criador do PEI pode finaliza-lo');
        }

        DB::transaction(function () use ($pei) {
            $pei->update(['is_finished' => true]);
        });

        return $pei;
    }

    /**
     * Remove o PEI e todos os registros vinculados (cascade).
     */
    public function delete(Pei $pei): void
    {
        if ($pei->creator_id !== auth()->id()) {
            throw new \Exception('Acesso negado: apenas o criador deste PEI pode excluí-lo.');
        }

        DB::transaction(function () use ($pei) {
            $pei->delete();
        });
    }

    public function getCurrent(Student $student): ?Pei
    {
        return Pei::where('student_id', $student->id)
            ->where('is_current', true)
            ->first();
    }

    public function removeCurrent(Pei $pei)
    {
        $pei->update(['is_current' => false]); 
    }

    public function cancelVersion(Pei $pei)
    {
        if ($pei->is_current) {

            $previous = Pei::where('student_id', $pei->student_id)
            ->where('version', '<', $pei->version)
            ->orderByDesc('version')
            ->first();

            if ($previous) {
                $previous->update(['is_current' => true]);
            }
        }
    }
}