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

        // 🔹 se for professor, filtra pelos cursos dele
        if ($user->teacher_id) {
            $teacherCourseIds = $user->teacher
                ->courses()
                ->pluck('courses.id');

            $query->whereIn('course_id', $teacherCourseIds);
        }

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
        $user = auth()->user();

        $query = Pei::query()
            ->where('student_id', $student->id)
            ->with(['student.person', 'semester']);

        if ($user->teacher_id) {
            $teacherCourseIds = $user->teacher
                ->courses()
                ->pluck('courses.id');

            $query->whereIn('course_id', $teacherCourseIds);
        }

        return $query
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
            // Verifica se já existe qualquer PEI para este aluno
            $currentPei = $this->getCurrent($student);

            if ($currentPei) {
                // Se já existe, redireciona para a lógica de nova versão
                return $this->createVersion($student);
            }

            // Lógica para o PRIMEIRO PEI (Versão 1)
            $studentCourse = $student->currentCourse()->first();
            if (!$studentCourse) {
                throw new \Exception('Este aluno não possui matrícula vigente');
            }
            
            $course = $studentCourse->course;
            $semesterId = $this->semesterService->getCurrent()?->id;

            if (!$semesterId) {
                throw new \Exception('O sistema não possui semestre atual cadastrado');
            }

            $currentContext = $student->contexts()->where('is_current', true)->first();
            if (!$currentContext) {
                throw new \Exception('Este aluno não possui um contexto atual');
            }

            return Pei::create([
                'student_id'         => $student->id,
                'creator_id'         => Auth::id(),
                'semester_id'        => $semesterId,
                'course_id'          => $course->id,
                'student_context_id' => $currentContext->id,
                'is_finished'        => false,
                'version'            => 1,
                'is_current'         => true,
            ]);
        });
    }

    /**
     * Cria uma nova versão limpa a partir de um PEI finalizado.
     */
    public function createVersion(Student $student): Pei
    {
        return DB::transaction(function () use ($student) {
            $pei = $this->getCurrent($student);

            if (!$pei) {
                throw new \DomainException('Aluno não possui PEI para gerar versão.');
            }

            // Regra: Só cria nova versão se o atual estiver finalizado
            if (!$pei->is_finished) {
                throw new \Exception('O PEI atual ainda está em andamento. Finalize-o antes de criar uma nova versão.');
            }

            $semesterId = $this->semesterService->getCurrent()?->id;
            if (!$semesterId) {
                throw new \DomainException('O sistema não possui semestre atual cadastrado');
            }

            $studentContext = $student->contexts()->where('is_current', true)->first();
            if (!$studentContext) {
                throw new \Exception('Este aluno não possui um Contexto atual definido.');
            }

            // Remove o flag de 'atual' do antigo
            $this->removeCurrent($pei);

            // Cria o novo PEI (apenas cabeçalho, sem replicar disciplinas)
            return Pei::create([
                'creator_id'         => Auth::id(),
                'student_id'         => $student->id,
                'semester_id'        => $semesterId,
                'course_id'          => $pei->course_id,
                'student_context_id' => $studentContext->id,
                'is_finished'        => false,
                'version'            => ($pei->version ?? 1) + 1,
                'is_current'         => true,
            ]);
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
            $pei->update(['is_finished' => true, 'is_current' => true,]);
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