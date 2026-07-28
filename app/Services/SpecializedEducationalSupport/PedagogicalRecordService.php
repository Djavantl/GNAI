<?php

namespace App\Services\SpecializedEducationalSupport;

use App\Domains\SpecializedEducationalSupport\Domain\Enums\AttendanceType;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\SessionStatus;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Session;
use App\Models\SpecializedEducationalSupport\PedagogicalRecord;
use App\Models\SpecializedEducationalSupport\Student;
use App\Support\RichTextSanitizer;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PedagogicalRecordService
{
    private function userCanViewOnlyOwn(): bool
    {
        $user = Auth::user();

        return !($user?->can('session-record.view-all') ?? false)
            && ($user?->can('session-record.view-own') ?? false);
    }

    private function applyOwnVisibilityFilter(Builder $query): void
    {
        if (!$this->userCanViewOnlyOwn()) {
            return;
        }

        $professionalId = Auth::user()?->professional?->id;

        if ($professionalId) {
            $query->whereHas('attendanceSession', fn ($q) => $q->where('professional_id', $professionalId));
        } else {
            $query->whereRaw('1 = 0');
        }
    }

    private function ensureCanView(PedagogicalRecord $record): void
    {
        if (!$this->userCanViewOnlyOwn()) {
            return;
        }

        $record->loadMissing('attendanceSession');
        $professionalId = Auth::user()?->professional?->id;

        if (!$professionalId || (int) $record->attendanceSession?->professional_id !== (int) $professionalId) {
            throw new Exception('Você não tem permissão para ver atendimentos pedagógicos de outros profissionais.');
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
        if (! SessionStatus::isScheduledValue($session->status)) {
            throw new Exception("O agendamento precisa estar com status Agendada para {$action}.");
        }
    }

    private function ensurePedagogicalSession(Session $session): void
    {
        if (! AttendanceType::isPedagogical($session->attendance_type)) {
            throw new Exception('Este agendamento não está classificado como Atendimento Pedagógico.');
        }

        if ($session->students()->count() !== 1) {
            throw new Exception('Atendimentos pedagógicos devem possuir exatamente um aluno.');
        }
    }

    public function ensureCanCreateForSession(Session $session): void
    {
        $this->ensureAssignedProfessional($session, 'criar o atendimento pedagógico');
        $this->ensureScheduledSession($session, 'criar o atendimento pedagógico');
        $this->ensurePedagogicalSession($session);

        if ($session->pedagogicalRecord()->exists()) {
            throw new Exception('Este agendamento já possui atendimento pedagógico cadastrado.');
        }

        if ($session->sessionRecord()->exists()) {
            throw new Exception('Este agendamento já possui Atendimento AEE e não pode receber atendimento pedagógico.');
        }
    }

    public function ensureCanManageRecord(PedagogicalRecord $record, string $action): void
    {
        $session = $record->attendanceSession()->withTrashed()->first();

        if (!$session) {
            throw new Exception('Agendamento vinculado ao atendimento pedagógico não encontrado.');
        }

        $this->ensureAssignedProfessional($session, $action);
    }

    public function create(array $data): PedagogicalRecord
    {
        $session = Session::with(['students', 'pedagogicalRecord', 'sessionRecord'])
            ->findOrFail($data['attendance_session_id']);

        $this->ensureCanCreateForSession($session);

        return DB::transaction(function () use ($session, $data) {
            $session->update(['status' => SessionStatus::COMPLETED_DATABASE_VALUE]);

            return PedagogicalRecord::create($this->payload($data))
                ->load(['attendanceSession.students.person', 'attendanceSession.professional.person']);
        });
    }

    public function getMyRecords(array $filters = [])
    {
        $professional = Auth::user()?->professional;

        if (!$professional) {
            abort(403, 'Acesso permitido apenas para profissionais.');
        }

        $query = PedagogicalRecord::query()
            ->with(['attendanceSession.students.person', 'attendanceSession.professional.person'])
            ->whereHas('attendanceSession', function ($q) use ($professional) {
                $q->where('professional_id', $professional->id);
            });

        if (!empty($filters['student'])) {
            $query->whereHas('attendanceSession.students', function ($q) use ($filters) {
                $q->where('students.id', $filters['student']);
            });
        }

        return $query
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();
    }

    public function show(PedagogicalRecord $record): PedagogicalRecord
    {
        $this->ensureCanView($record);

        return $record->load(['attendanceSession.students.person', 'attendanceSession.professional.person']);
    }

    public function update(PedagogicalRecord $record, array $data): PedagogicalRecord
    {
        $this->ensureCanManageRecord($record, 'editar este atendimento pedagógico');

        return DB::transaction(function () use ($record, $data) {
            $record->update($this->payload($data));

            return $record->fresh(['attendanceSession.students.person', 'attendanceSession.professional.person']);
        });
    }

    public function delete(PedagogicalRecord $record): void
    {
        $this->ensureCanManageRecord($record, 'excluir este atendimento pedagógico');

        $record->delete();
    }

    public function studentRecords(Student $student, int $perPage = 5)
    {
        $query = PedagogicalRecord::query()
            ->with(['attendanceSession.professional.person', 'attendanceSession.students.person'])
            ->whereHas('attendanceSession.students', fn ($q) => $q->where('students.id', $student->id));

        $this->applyOwnVisibilityFilter($query);

        return $query->orderByDesc('id')->paginate($perPage, ['*'], 'pedagogical_page');
    }

    private function payload(array $data): array
    {
        $isPresent = (bool) ($data['is_present'] ?? false);

        return [
            'attendance_session_id' => $data['attendance_session_id'],
            'duration' => $data['duration'],
            'is_present' => $isPresent,
            'absence_reason' => $isPresent ? null : RichTextSanitizer::sanitize((string) ($data['absence_reason'] ?? '')),
            'planned_performed_activities' => $isPresent ? RichTextSanitizer::sanitize((string) ($data['planned_performed_activities'] ?? '')) : null,
            'pedagogical_record' => $isPresent ? RichTextSanitizer::sanitize((string) ($data['pedagogical_record'] ?? '')) : null,
            'resources_used' => $isPresent && isset($data['resources_used']) ? RichTextSanitizer::sanitize((string) $data['resources_used']) : null,
            'general_observations' => $isPresent && isset($data['general_observations']) ? RichTextSanitizer::sanitize((string) $data['general_observations']) : null,
        ];
    }
}
