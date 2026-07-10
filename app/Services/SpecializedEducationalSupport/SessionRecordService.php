<?php

namespace App\Services\SpecializedEducationalSupport;

use App\Models\SpecializedEducationalSupport\SessionRecord;
use App\Models\SpecializedEducationalSupport\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\SpecializedEducationalSupport\StudentSessionEvaluation;
use Exception;

class SessionRecordService
{
    private function normalizeStatus(?string $status): string
    {
        return mb_strtolower(trim((string) $status));
    }

    private function userCanViewAll(): bool
    {
        return Auth::user()?->can('session-record.view-all') ?? false;
    }

    private function userCanViewOnlyOwn(): bool
    {
        $user = Auth::user();
        return !($user?->can('session-record.view-all') ?? false)
            && ($user?->can('session-record.view-own') ?? false);
    }

    private function applyOwnVisibilityFilter(\Illuminate\Database\Eloquent\Builder $query): void
    {
        if (!$this->userCanViewOnlyOwn()) {
            return;
        }

        $professionalId = Auth::user()?->professional?->id;

        if ($professionalId) {
            $query->whereHas('sessionRecord.attendanceSession', function ($q) use ($professionalId) {
                $q->where('professional_id', $professionalId);
            });
        } else {
            $query->whereRaw('1 = 0');
        }
    }

    private function ensureOwnVisibilityAccess(?Session $session): void
    {
        if (!$this->userCanViewOnlyOwn() || !$session) {
            return;
        }

        $professionalId = Auth::user()?->professional?->id;

        abort_unless(
            $professionalId && (int) $session->professional_id === (int) $professionalId,
            403,
            'Você só pode visualizar registros de seus próprios agendamentos.'
        );
    }

    private function ensureCanViewRecord(SessionRecord $sessionRecord): void
    {
        $sessionRecord->loadMissing('attendanceSession');
        $session = $sessionRecord->attendanceSession;

        if (!$this->userCanViewOnlyOwn() || !$session) {
            return;
        }

        $professionalId = Auth::user()?->professional?->id;

        if (!$professionalId || (int) $session->professional_id !== (int) $professionalId) {
            throw new Exception('Você não tem permissão para ver registros de outros profissionais.');
        }
    }

    private function ensureAssignedProfessional(Session $session, string $action): void
    {
        $professionalId = Auth::user()?->professional?->id;

        if (!$professionalId || (int) $professionalId !== (int) $session->professional_id) {
            throw new Exception("Apenas o profissional vinculado a este agendamento pode {$action}.");
        }
    }

    private function ensureScheduledSession(Session $session, string $action): void
    {
        if (!in_array($this->normalizeStatus($session->status), ['agendada', 'agendado', 'scheduled'], true)) {
            throw new Exception("O agendamento precisa estar com status Agendada para {$action}.");
        }
    }

    private function normalizeEvaluationPayload(array $evalData): array
    {
        $isPresent = isset($evalData['is_present'])
            ? filter_var($evalData['is_present'], FILTER_VALIDATE_BOOLEAN)
            : false;

        return [
            'student_id' => $evalData['student_id'],
            'is_present' => $isPresent,
            'absence_reason' => $isPresent ? null : ($evalData['absence_reason'] ?? null),
            'student_participation' => $isPresent ? ($evalData['student_participation'] ?? null) : null,
            'adaptations_made' => $isPresent ? ($evalData['adaptations_made'] ?? null) : null,
            'development_evaluation' => $isPresent ? ($evalData['development_evaluation'] ?? null) : null,
            'progress_indicators' => $isPresent ? ($evalData['progress_indicators'] ?? null) : null,
            'recommendations' => $isPresent ? ($evalData['recommendations'] ?? null) : null,
            'next_session_adjustments' => $isPresent ? ($evalData['next_session_adjustments'] ?? null) : null,
        ];
    }

    public function ensureCanCreateForSession(Session $session): void
    {
        $this->ensureAssignedProfessional($session, 'criar o registro deste agendamento');
        $this->ensureScheduledSession($session, 'criar o registro deste agendamento');

        if ($session->sessionRecord()->exists()) {
            throw new Exception('Este agendamento já possui registro cadastrado.');
        }
    }

    public function ensureCanManageRecord(SessionRecord $sessionRecord, string $action): void
    {
        $session = $sessionRecord->attendanceSession()->withTrashed()->first();

        if (!$session) {
            throw new Exception('Agendamento vinculado ao registro não encontrado.');
        }

        $this->ensureAssignedProfessional($session, $action);
    }

    public function ensureCanManageEvaluation(StudentSessionEvaluation $evaluation, string $action): void
    {
        $evaluation->loadMissing('sessionRecord.attendanceSession');

        $session = $evaluation->sessionRecord?->attendanceSession;

        if (!$session) {
            throw new Exception('Agendamento vinculado à avaliação não encontrado.');
        }

        $this->ensureAssignedProfessional($session, $action);
    }

    public function getMyRecords(array $filters = [])
    {
        $professional = Auth::user()?->professional;

        if (!$professional) {
            abort(403, 'Acesso permitido apenas para profissionais.');
        }

        $query = SessionRecord::with([
            'attendanceSession.professional.person',
            'studentEvaluations.student.person',
        ])
        ->whereHas('attendanceSession', function ($q) use ($professional) {
            $q->where('professional_id', $professional->id);
        });

        if (!empty($filters['student'])) {
            $query->whereHas('studentEvaluations', function ($q) use ($filters) {
                $q->where('student_id', $filters['student']);
            });
        }

        return $query
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();
    }

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
        $session = Session::with('sessionRecord')->findOrFail($data['attendance_session_id']);
        $this->ensureCanCreateForSession($session);

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
        $this->ensureCanViewRecord($session_rec);

        return $session_rec->load(['attendanceSession', 'studentEvaluations.student']);
    }

    /**
     * Atualiza o Registro Geral e sincroniza as Avaliações
     */
    public function update(SessionRecord $session_rec, array $data): SessionRecord
    {
        $this->ensureCanManageRecord($session_rec, 'editar este registro de atendimento');

        return DB::transaction(function () use ($session_rec, $data) {
            // Atualiza o registro geral do agendamento
            $session_rec->update([
                'attendance_session_id' => $data['attendance_session_id'] ?? $session_rec->attendance_session_id,
                'duration' => $data['duration'],
                'activities_performed' => $data['activities_performed'],
                'strategies_used' => $data['strategies_used'] ?? null,
                'resources_used' => $data['resources_used'] ?? null,
                'general_observations' => $data['general_observations'] ?? null,
            ]);

            foreach ($data['evaluations'] as $evalData) {
                $normalizedData = $this->normalizeEvaluationPayload($evalData);

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
        $this->ensureCanManageRecord($session_rec, 'excluir este registro de atendimento');

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

        $this->applyOwnVisibilityFilter($query);

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

        $evaluation->loadMissing('sessionRecord.attendanceSession');
        $this->ensureCanViewRecord($evaluation->sessionRecord);

        return $evaluation->load([
            'student.person',
            'sessionRecord.attendanceSession.professional.person',
        ]);
    }

    public function studentUpdate(Student $student, StudentSessionEvaluation $evaluation, array $data): StudentSessionEvaluation
    {
        abort_unless($evaluation->student_id === $student->id, 404);
        $this->ensureCanManageEvaluation($evaluation, 'editar esta avaliação do aluno');

        return DB::transaction(function () use ($evaluation, $data) {
            $evaluation->update($this->normalizeEvaluationPayload($data));

            return $evaluation->fresh([
                'student.person',
                'sessionRecord.attendanceSession.professional.person',
            ]);
        });
    }

    public function studentDelete(Student $student, StudentSessionEvaluation $evaluation): void
    {
        abort_unless($evaluation->student_id === $student->id, 404);
        $this->ensureCanManageEvaluation($evaluation, 'excluir esta avaliação do aluno');

        $evaluation->delete();
    }


    public function studentPdfData(SessionRecord $sessionRecord, Student $student): SessionRecord
    {
        $this->ensureCanViewRecord($sessionRecord);

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
