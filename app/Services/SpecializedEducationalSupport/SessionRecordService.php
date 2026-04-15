<?php

namespace App\Services\SpecializedEducationalSupport;

use App\Models\SpecializedEducationalSupport\SessionRecord;
use App\Models\SpecializedEducationalSupport\Session;
use Illuminate\Support\Facades\DB;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\SpecializedEducationalSupport\StudentSessionEvaluation;

class SessionRecordService
{
    /**
     * Lista todos os registros com as avaliações e alunos carregados
     */
    public function index()
    {
        return SessionRecord::with([
            'attendanceSession.professional.person',
            'studentEvaluations.student.person',
        ])
        ->orderByDesc('id')
        ->paginate(10)
        ->withQueryString();
    }

    /**
     * Cria o Registro Geral e todas as Avaliações Individuais
     */
    public function create(array $data): SessionRecord
    {
        $session = Session::where('id', $data['attendance_session_id']);

        return DB::transaction(function () use ($session, $data) {

            $session->update(['status' => 'Realizada']);

            // 1. Cria o registro principal (o que o profissional fez)
            $sessionRecord = SessionRecord::create([
                'attendance_session_id' => $data['attendance_session_id'],
                'duration'              => $data['duration'],
                'activities_performed'  => $data['activities_performed'],
                'strategies_used'       => $data['strategies_used'] ?? null,
                'resources_used'        => $data['resources_used'] ?? null,
                'general_observations'  => $data['general_observations'] ?? null,
            ]);

            // 2. Cria as avaliações de cada aluno enviado no array 'evaluations'
            foreach ($data['evaluations'] as $evaluation) {
                $sessionRecord->studentEvaluations()->create($evaluation);
            }

            return $sessionRecord->load('studentEvaluations');
        });
    }

    /**
     * Exibe um registro específico com seus relacionamentos
     */
    public function show(SessionRecord $session_rec): SessionRecord
    {
        return $session_rec->load(['attendanceSession', 'studentEvaluations.student']);
    }

    /**
     * Atualiza o Registro Geral e sincroniza as Avaliações
     */
    public function update(SessionRecord $session_rec, array $data): SessionRecord
    {
        return DB::transaction(function () use ($session_rec, $data) {
            // Atualiza o registro geral da sessão
            $session_rec->update([
                'attendance_session_id' => $data['attendance_session_id'] ?? $session_rec->attendance_session_id,
                'duration' => $data['duration'],
                'activities_performed' => $data['activities_performed'],
                'strategies_used' => $data['strategies_used'] ?? null,
                'resources_used' => $data['resources_used'] ?? null,
                'general_observations' => $data['general_observations'] ?? null,
            ]);

            foreach ($data['evaluations'] as $evalData) {
                $isPresent = isset($evalData['is_present'])
                    ? filter_var($evalData['is_present'], FILTER_VALIDATE_BOOLEAN)
                    : false;

                $normalizedData = [
                    'student_id' => $evalData['student_id'],
                    'is_present' => $isPresent,
                    'absence_reason' => $isPresent ? null : ($evalData['absence_reason'] ?? null),

                    // Se estiver presente, salva; se não, limpa tudo
                    'student_participation' => $isPresent ? ($evalData['student_participation'] ?? null) : null,
                    'adaptations_made' => $isPresent ? ($evalData['adaptations_made'] ?? null) : null,
                    'development_evaluation' => $isPresent ? ($evalData['development_evaluation'] ?? null) : null,
                    'progress_indicators' => $isPresent ? ($evalData['progress_indicators'] ?? null) : null,
                    'recommendations' => $isPresent ? ($evalData['recommendations'] ?? null) : null,
                    'next_session_adjustments' => $isPresent ? ($evalData['next_session_adjustments'] ?? null) : null,
                ];

                $session_rec->studentEvaluations()->updateOrCreate(
                    ['student_id' => $evalData['student_id']],
                    $normalizedData
                );
            }

            return $session_rec->fresh('studentEvaluations');
        });
    }

    public function delete(SessionRecord $session_rec): void
    {
        // O cascadeOnDelete na migration cuidará das avaliações automaticamente
        $session_rec->delete();
    }

    public function restore(SessionRecord $session_rec): SessionRecord
    {
        $session_rec->restore();
        // Opcional: restaurar avaliações se elas também usarem SoftDeletes
        $session_rec->studentEvaluations()->restore();

        return $session_rec;
    }

    public function forceDelete(SessionRecord $session_rec): void
    {
        $session_rec->forceDelete();
    }

    

    public function studentIndex(Student $student, array $filters = [], int $perPage = 10)
    {
        $query = StudentSessionEvaluation::query()
            ->with([
                'sessionRecord.attendanceSession.professional.person',
            ])
            ->where('student_id', $student->id);

        if (!empty($filters['professional_id'])) {
            $query->whereHas(
                'sessionRecord.attendanceSession.professional',
                function ($q) use ($filters) {
                    $q->where('id', $filters['professional_id']);
                }
            );
        }

        if (isset($filters['is_present']) && $filters['is_present'] !== '') {
            $query->where(
                'is_present',
                (bool) $filters['is_present']
            );
        }

        return $query
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function studentShow(Student $student, StudentSessionEvaluation $evaluation): StudentSessionEvaluation
    {
        abort_unless($evaluation->student_id === $student->id, 404);

        return $evaluation->load([
            'student.person',
            'sessionRecord.attendanceSession.professional.person',
        ]);
    }


    public function studentPdfData(SessionRecord $sessionRecord, Student $student): SessionRecord
    {
        $sessionRecord->load([
            'attendanceSession.professional.person',
        ]);

        $evaluation = $sessionRecord->studentEvaluations()
            ->with(['student.person'])
            ->where('student_id', $student->id)
            ->firstOrFail();

        $sessionRecord->setRelation('studentEvaluations', collect([$evaluation]));

        return $sessionRecord;
    }
}