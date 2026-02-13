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

class PeiService
{
    protected $semesterService;

    public function __construct(SemesterService $semesterService)
    {
        $this->semesterService = $semesterService;
    }

    /**
     * Lista todos os PEIs de um estudante específico.
     */
    public function index(Student $student): Collection
    {
        return Pei::where('student_id', $student->id)
            ->with(['discipline', 'semester'])
            ->get();
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
            $semester = $this->semesterService->getCurrent();

            if (!$semester) {
                throw new \Exception('Não há semestre atual definido para gerar o PEI.');
            }

            $professional = Auth::id();

            $data['professional_id'] = $professional;
            $data['semester_id'] = $semester->id;
            $data['is_finished'] = false;

            return Pei::create($data);
        });
    }

    /**
     * Atualiza os dados básicos do PEI.
     */
    public function update(Pei $pei, array $data): Pei
    {
        DB::transaction(function () use ($pei, $data) {
            $pei->update($data);
        });

        return $pei;
    }

    /**
     * Finaliza o PEI, impedindo novas edições se necessário.
     */
    public function finish(Pei $pei): Pei
    {
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
        return DB::transaction(function () use ($pei, $data) {
            return $pei->specificObjectives()->create($data);
        });
    }

    public function updateObjective(SpecificObjective $objective, array $data): SpecificObjective
    {
        DB::transaction(function () use ($objective, $data) {
            $objective->update($data);
        });

        return $objective;
    }


    public function deleteObjective(SpecificObjective $objective): void
    {
        $objective->delete();
    }

   /**
     * Adiciona conteúdo programático adaptado.
     */
    public function addContent(Pei $pei, array $data): ContentProgrammatic
    {
        return DB::transaction(function () use ($pei, $data) {
            return $pei->contentProgrammatic()->create($data);
        });
    }

    /**
     * Atualiza conteúdo programático adaptado.
     */
    public function updateContent(ContentProgrammatic $content, array $data): ContentProgrammatic
    {
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
        $content->delete();
    }

   /**
     * Adiciona metodologia 
     */
    public function addMethodology(Pei $pei, array $data): Methodology
    {
        return DB::transaction(function () use ($pei, $data) {
            return $pei->methodologies()->create($data);
        });
    }

    /**
     * Atualiza metodologia 
     */
    public function updateMethodology(Methodology $methodology, array $data): Methodology
    {
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
        $methodology->delete();
    }
}