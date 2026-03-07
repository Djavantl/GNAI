<?php

namespace App\Services\SpecializedEducationalSupport;

use App\Models\SpecializedEducationalSupport\Pei;
use App\Models\SpecializedEducationalSupport\PeiDiscipline;
use App\Models\SpecializedEducationalSupport\Teacher;
use App\Models\SpecializedEducationalSupport\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class PeiDisciplineService
{
    /**
     * Verifica se o PEI está finalizado e lança exceção se estiver.
     * Isso centraliza a regra de "Somente Leitura".
     */
    private function checkPeiStatus(Pei $pei)
    {
        if ($pei->is_finished) {
            throw new Exception("Este PEI já foi finalizado e não permite alterações.");
        }
    }

    /**
     * Lista adaptações (pei_disciplines) de um PEI
     */
    public function listByPei(Pei $pei)
    {
        // Visualização é permitida mesmo finalizado
        return $pei->peiDisciplines()->with(['teacher', 'discipline', 'creator'])->get();
    }

    /**
     * Cria um novo PeiDiscipline com validações de compatibilidade.
     *
     * Regras aplicadas:
     *  - disciplina deve pertencer ao curso do PEI / curso atual do aluno
     *  - disciplina deve fazer parte da grade do curso
     *  - professor deve lecionar a disciplina e estar vinculado ao curso
     */
    public function store(Pei $pei, array $data): PeiDiscipline
    {
        $this->checkPeiStatus($pei);

        // validações antes da transação
        $disciplineId = $data['discipline_id'] ?? null;
        $teacherId = $data['teacher_id'] ?? null;

        if (!$disciplineId) {
            throw new Exception("Disciplina não informada.");
        }

        if (!$teacherId) {
            throw new Exception("Professor não informado.");
        }

        $exists = PeiDiscipline::where('pei_id', $pei->id)->where('discipline_id', $disciplineId)->first();
        if ($exists) {
            throw new Exception("Já existe uma adaptação para essa disciplina nesse PEI. Caso precise de alterações edite à existente");
        }

        $this->assertDisciplineInStudentCourse($pei, (int) $disciplineId);
        $this->assertDisciplineInCourseGrade($pei, (int) $disciplineId);
        $this->assertTeacherCompatibleWithDisciplineAndCourse($pei, (int) $teacherId, (int) $disciplineId);

        return DB::transaction(function () use ($pei, $data) {
            return PeiDiscipline::create([
                'pei_id' => $pei->id,
                'teacher_id' => $data['teacher_id'],
                'discipline_id' => $data['discipline_id'],
                'creator_id' => Auth::id(),
                'specific_objectives' => $data['specific_objectives'] ?? null,
                'content_programmatic' => $data['content_programmatic'] ?? null,
                'methodologies' => $data['methodologies'] ?? null,
                'evaluations' => $data['evaluations'] ?? null,
            ]);
        });
    }

    /**
     * Atualiza uma PeiDiscipline com as mesmas validações aplicadas na criação
     */
    public function update(PeiDiscipline $peiDiscipline, array $data): PeiDiscipline
    {
        // Verifica o status através do relacionamento com o PEI pai
        $this->checkPeiStatus($peiDiscipline->pei);

        // somente validar quando os campos relevantes mudarem ou estiverem presentes
        $disciplineId = $data['discipline_id'] ?? $peiDiscipline->discipline_id;
        $teacherId = $data['teacher_id'] ?? $peiDiscipline->teacher_id;

        if (!$disciplineId) {
            throw new Exception("Disciplina não informada.");
        }

        if (!$teacherId) {
            throw new Exception("Professor não informado.");
        }

        $this->assertDisciplineInStudentCourse($peiDiscipline->pei, (int) $disciplineId);
        $this->assertDisciplineInCourseGrade($peiDiscipline->pei, (int) $disciplineId);
        $this->assertTeacherCompatibleWithDisciplineAndCourse($peiDiscipline->pei, (int) $teacherId, (int) $disciplineId);

        return DB::transaction(function () use ($peiDiscipline, $data) {
            $peiDiscipline->update([
                'teacher_id' => $data['teacher_id'],
                'discipline_id' => $data['discipline_id'],
                'specific_objectives' => $data['specific_objectives'] ?? $peiDiscipline->specific_objectives,
                'content_programmatic' => $data['content_programmatic'] ?? $peiDiscipline->content_programmatic,
                'methodologies' => $data['methodologies'] ?? $peiDiscipline->methodologies,
                'evaluations' => $data['evaluations'] ?? $peiDiscipline->evaluations,
            ]);

            return $peiDiscipline;
        });
    }

    public function delete(PeiDiscipline $peiDiscipline): bool
    {
        $this->checkPeiStatus($peiDiscipline->pei);

        return $peiDiscipline->delete();
    }

    public function find(PeiDiscipline $peiDiscipline): PeiDiscipline
    {
        // Visualização permitida
        return $peiDiscipline->load(['teacher', 'discipline', 'pei', 'creator']);
    }

    /**
     * Verifica se a disciplina pertence ao curso atual do aluno (ou ao course do PEI).
     * Lança Exception se não pertencer.
     */
    private function assertDisciplineInStudentCourse(Pei $pei, int $disciplineId)
    {
        // tenta obter course_id do PEI ou do contexto do aluno (currentCourse)
        $courseId = $pei->course_id ?? $pei->student->currentCourse?->course_id ?? null;

        if (!$courseId) {
            throw new Exception("Curso do PEI / curso atual do aluno não está definido.");
        }

        $course = Course::find($courseId);
        if (!$course) {
            throw new Exception("Curso relacionado ao PEI não encontrado (id: {$courseId}).");
        }

        // assume-se relação disciplines() no model Course
        $has = $course->disciplines()
            ->where('disciplines.id', $disciplineId)
            ->exists();

        if (!$has) {
            throw new Exception("Disciplina selecionada (id: {$disciplineId}) não faz parte do curso do aluno (id: {$courseId}).");
        }
    }

    /**
     * Verifica se a disciplina faz parte da grade do curso.
     * (Se a aplicação separa 'grade' de 'curso', adaptar aqui; por enquanto checamos via relação Course->disciplines())
     */
    private function assertDisciplineInCourseGrade(Pei $pei, int $disciplineId)
    {
        // reusa a lógica do course do PEI
        $courseId = $pei->course_id ?? $pei->student->currentCourse?->course_id ?? null;

        if (!$courseId) {
            throw new Exception("Curso do PEI / curso atual do aluno não está definido.");
        }

        $course = Course::find($courseId);
        if (!$course) {
            throw new Exception("Curso relacionado ao PEI não encontrado (id: {$courseId}).");
        }

        // Se sua aplicação tem uma tabela/relacionamento distinto para 'grade' substitua esta checagem.
        $inGrade = $course->disciplines()
            ->where('disciplines.id', $disciplineId)
            ->exists();

        if (!$inGrade) {
            throw new Exception("Disciplina (id: {$disciplineId}) não está presente na grade do curso (id: {$courseId}).");
        }
    }

    /**
     * Verifica se o professor está compatível com a disciplina e com o curso.
     * - professor deve lecionar a disciplina
     * - professor deve estar vinculado ao curso
     */
    private function assertTeacherCompatibleWithDisciplineAndCourse(Pei $pei, int $teacherId, int $disciplineId)
    {
        $teacher = Teacher::find($teacherId);
        if (!$teacher) {
            throw new Exception("Professor não encontrado (id: {$teacherId}).");
        }

        // verifica se o professor leciona a disciplina
        $teachesDiscipline = $teacher->disciplines()
            ->where('disciplines.id', $disciplineId)
            ->exists();
        if (!$teachesDiscipline) {
            throw new Exception("O professor (id: {$teacherId}) não está associado a essa disciplina (id: {$disciplineId}).");
        }

        // verifica se o professor está vinculado ao course do PEI / curso atual do aluno
        $courseId = $pei->course_id ?? $pei->student->currentCourse?->course_id ?? null;
        if (!$courseId) {
            throw new Exception("Curso do PEI / curso atual do aluno não está definido.");
        }

        $teachesInCourse = $teacher->courses()->where('courses.id', $courseId)->exists();
        if (!$teachesInCourse) {
            throw new Exception("O professor (id: {$teacherId}) não está vinculado ao curso (id: {$courseId}).");
        }
    }
}