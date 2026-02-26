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
                'discipline'
            ]);

        // Se for professor → apenas disciplinas dele
        if ($user->teacher_id) {
            $teacher = $user->teacher;

            $query->whereIn('discipline_id', function ($q) use ($teacher) {
                $q->select('discipline_id')
                ->from('discipline_teacher') // tabela pivot teacher ↔ discipline
                ->where('teacher_id', $teacher->id);
            });
        }

        return $query
            ->student($filters['student_id'] ?? null)
            ->semester($filters['semester_id'] ?? null)
            ->discipline($filters['discipline_id'] ?? null)
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
            ->with(['student.person', 'semester', 'discipline'])
            ->semester($filters['semester_id'] ?? null)
            ->discipline($filters['discipline_id'] ?? null)
            ->finished($filters['is_finished'] ?? null)
            ->version($filters['version'] ?? null)
            ->latest()
            ->paginate(10)
            ->withQueryString();
    }
    /**
     * Mostra os detalhes de um PEI com todas as suas tabelas auxiliares.
     */
    public function show(Pei $pei): Pei
    {
        return $pei->load([
            'student', 
            'studentContext', 
            'specificObjectives', 
            'contentProgrammatic', 
            'methodologies'
        ]);
    }

    /**
     * Cria a estrutura inicial do PEI vinculada ao contexto atual do aluno.
     */
    public function create(array $data): Pei
    {
        return DB::transaction(function () use ($data) {
            $data['creator_id'] = Auth::id();
            
            return $this->prepareAndSavePei($data);
        });
    }

    public function createAsTeacher(array $data): Pei
    {
        return DB::transaction(function () use ($data) {
            $user = auth()->user();
            
            if (!$user->teacher_id) {
                throw new \Exception('Usuário não possui perfil de professor vinculado.');
            }

            // Vincula o ID do professor e o nome (para histórico/redundância se desejar)
            $data['teacher_id'] = $user->teacher_id;
            $data['creator_id'] = $user->id;
            
            // Opcional: Salvar o nome do professor também na coluna de string 
            // para manter compatibilidade com as listagens que usam teacher_name
            $data['teacher_name'] = $user->person->name ?? $user->name;

            return $this->prepareAndSavePei($data);
        });
    }

    private function prepareAndSavePei(array $data): Pei
    {
        $student = Student::findOrFail($data['student_id']);
        $studentCourse = $student->currentCourse()->firstOrFail();

        // Dados automáticos do sistema
        $data['course_id'] = $studentCourse->course_id;
        $data['semester_id'] = $this->semesterService->getCurrent()?->id;
        $data['student_context_id'] = $student->contexts()->where('is_current', true)->first()?->id;

        if (!$data['semester_id']) {
            throw new \Exception('Não há semestre atual definido.');
        }

        // Validação de duplicidade (considerando curso e disciplina)
        $exists = Pei::where('student_id', $data['student_id'])
            ->where('course_id', $data['course_id'])
            ->where('discipline_id', $data['discipline_id'])
            ->exists();

        if ($exists) {
            throw new \Exception('Já existe um PEI para este aluno nesta disciplina. Use a funcionalidade "Nova Versão".');
        }

        $data['is_finished'] = false;
        $data['version'] = 1;
        $data['is_current'] = false;

        return Pei::create($data);
    }

    public function createVersion(Pei $pei): Pei
    {
        if (! $pei->is_finished) {
            throw new \Exception('Só é possível criar nova versão a partir de um PEI finalizado.');
        }

        return DB::transaction(function () use ($pei) {

            
            $student = $pei->student;

            $studentContext = $student->currentContext()->first();

            if (!$studentContext) {
                throw new \Exception('Este aluno não possui um contexto Contexto atual definido.');
            }

            // desativa versão corrente anterior
            $pei->update(['is_current' => false]);

            $user = auth()->user();
            // replica campos básicos
            $new = Pei::create([
                'student_id' => $pei->student_id,
                'teacher_id' => Auth::id(), 
                'semester_id' => $pei->semester_id,
                'course_id' => $pei->course_id,
                'discipline_id' => $pei->discipline_id,
                'teacher_id'      => $pei->teacher_id, 
                'teacher_name'    => $pei->teacher_name,
                'student_context_id' => $studentContext->id,
                'is_finished' => false,
                'version' => ($pei->version ?? 1) + 1,
                'is_current' => false,
                'creator_id' => $user->id,
                
            ]);

            // copiar objetivos
            foreach ($pei->specificObjectives as $obj) {
                $new->specificObjectives()->create($obj->replicate()->toArray());
            }

            // copiar conteúdos
            foreach ($pei->contentProgrammatic as $c) {
                $new->contentProgrammatic()->create($c->replicate()->toArray());
            }

            // copiar metodologias
            foreach ($pei->methodologies as $m) {
                $new->methodologies()->create($m->replicate()->toArray());
            }

            return $new;
        });
    }

    /**
     * Atualiza os dados básicos do PEI.
     */
    public function update(Pei $pei, array $data): Pei
    {
        if ($pei->is_finished) {
            throw new \Exception('PEI finalizado não pode ser alterado. Crie nova versão se precisar.');
        }

        DB::transaction(function () use ($pei, $data) {
            $pei->update($data);
        });

        return $pei;
    }

    public function setAsCurrent(Pei $pei): Pei
    {
        return DB::transaction(function () use ($pei) {

            // 1️⃣ desativa qualquer outro atual desse mesmo contexto
            Pei::where('student_id', $pei->student_id)
                ->where('course_id', $pei->course_id)
                ->where('discipline_id', $pei->discipline_id)
                ->where('is_current', true)
                ->update(['is_current' => false]);

            // 2️⃣ ativa o escolhido
            $pei->update(['is_current' => true]);

            return $pei;
        });
    }

    public function makeCurrent(Pei $pei): Pei
    {
        if (!$pei->is_finished) {
            throw new \Exception('PEI não finalizado: não é possível torna-lo atual.');
        }
        return $this->setAsCurrent($pei);
    }

    /**
     * Finaliza o PEI, impedindo novas edições se necessário.
     */
    public function finish(Pei $pei): Pei
    {
        DB::transaction(function () use ($pei) {
            $pei->update(['is_finished' => true]);
            $this->setAsCurrent($pei);
        });

        return $pei;
    }

    /**
     * Remove o PEI e todos os registros vinculados (cascade).
     */
    public function delete(Pei $pei): void
    {
        DB::transaction(function () use ($pei) {
            $pei->delete();
        });
    }

    // --- Métodos para Tabelas Auxiliares (Objetivos, Conteúdos e Metodologias) ---

    /**
     * Adiciona um objetivo específico ao PEI.
     */
    public function addObjective(Pei $pei, array $data): SpecificObjective
    {
        if ($pei->is_finished) {
            throw new \Exception('PEI finalizado: não é possível adicionar objetivos.');
        }

        return DB::transaction(function () use ($pei, $data) {
            return $pei->specificObjectives()->create($data);
        });
    }

    public function updateObjective(SpecificObjective $objective, array $data): SpecificObjective
    {
        if ($objective->pei->is_finished) {
            throw new \Exception('PEI finalizado: não é possível alterar objetivos.');
        }
        DB::transaction(function () use ($objective, $data) {
            $objective->update($data);
        });

        return $objective;
    }


    public function deleteObjective(SpecificObjective $objective): void
    {
        
        if ($objective->pei->is_finished) {
            throw new \Exception('PEI finalizado: não é possível excluir objetivos.');
        }

        $objective->delete();
    }

   /**
     * Adiciona conteúdo programático adaptado.
     */
    public function addContent(Pei $pei, array $data): ContentProgrammatic
    {
        if ($pei->is_finished) {
            throw new \Exception('PEI finalizado: não é possível adicionar conteúdos.');
        }

        return DB::transaction(function () use ($pei, $data) {
            return $pei->contentProgrammatic()->create($data);
        });
    }

    /**
     * Atualiza conteúdo programático adaptado.
     */
    public function updateContent(ContentProgrammatic $content, array $data): ContentProgrammatic
    {
        if ($content->pei->is_finished) {
            throw new \Exception('PEI finalizado: não é possível alterar conteúdos.');
        }

        DB::transaction(function () use ($content, $data) {
            $content->update($data);
        });

        return $content;
    }

    /**
     * Remove conteúdo programático adaptado.
     */
    public function deleteContent(ContentProgrammatic $content): void
    {
        if ($content->pei->is_finished) {
            throw new \Exception('PEI finalizado: não é possível excluir conteúdos.');
        }

        $content->delete();
    }

   /**
     * Adiciona metodologia 
     */
    public function addMethodology(Pei $pei, array $data): Methodology
    {
        if ($pei->is_finished) {
            throw new \Exception('PEI finalizado: não é possível adicionar metodologias.');
        }

        return DB::transaction(function () use ($pei, $data) {
            return $pei->methodologies()->create($data);
        });
    }

    /**
     * Atualiza metodologia 
     */
    public function updateMethodology(Methodology $methodology, array $data): Methodology
    {
        if ($methodology->pei->is_finished) {
            throw new \Exception('PEI finalizado: não é possível alterar metodologias.');
        }

        DB::transaction(function () use ($methodology, $data) {
            $methodology->update($data);
        });

        return $methodology;
    }

    /**
     * Remove metodologia .
     */
    public function deleteMethodology(Methodology $methodology): void
    {
        if ($methodology->pei->is_finished) {
            throw new \Exception('PEI finalizado: não é possível excluir metodologias.');
        }

        $methodology->delete();
    }
}