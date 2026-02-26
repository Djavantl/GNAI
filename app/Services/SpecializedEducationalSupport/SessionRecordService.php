<?php

namespace App\Services\SpecializedEducationalSupport;

use App\Models\SpecializedEducationalSupport\SessionRecord;
use App\Models\SpecializedEducationalSupport\Session;
use Illuminate\Support\Facades\DB;

class SessionRecordService
{
    /**
     * Lista todos os registros com as avaliações e alunos carregados
     */
    public function index()
    {
        return SessionRecord::with(['studentEvaluations.student'])->get();
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
            // Atualiza o pai
            $session_rec->update($data);

            // Atualiza os filhos
            foreach ($data['evaluations'] as $evalData) {
                $session_rec->studentEvaluations()->updateOrCreate(
                    ['student_id' => $evalData['student_id']],
                    $evalData
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
}