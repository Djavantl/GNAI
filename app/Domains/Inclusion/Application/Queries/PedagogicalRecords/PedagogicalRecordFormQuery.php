<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\PedagogicalRecords;

use App\Domains\Inclusion\Domain\Enums\AttendanceType;
use App\Domains\Inclusion\Domain\Enums\SessionStatus;
use App\Domains\Inclusion\Domain\Exceptions\InvalidPedagogicalRecord;
use App\Domains\Inclusion\Domain\Models\Guardian;
use App\Domains\Inclusion\Domain\Models\PedagogicalRecord;
use App\Domains\Inclusion\Domain\Models\Session;
use Carbon\Carbon;

final class PedagogicalRecordFormQuery
{
    public function forCreation(Session $session, int $professionalId): array
    {
        $session->load([
            'students.guardians.person',
            'professional.person',
            'aeeRecord',
            'pedagogicalRecord',
        ]);
        if ((int) $session->professional_id !== $professionalId || ! AttendanceType::isPedagogical($session->attendance_type) || ! SessionStatus::isScheduledValue($session->status) || ! $session->sessionDateHasArrived() || $session->students->count() !== 1 || $session->aeeRecord !== null || $session->pedagogicalRecord !== null) {
            throw new InvalidPedagogicalRecord('Este agendamento não está disponível para criação de registro pedagógico.');
        }

        return [
            'session' => $session,
            'context' => $this->context($session),
        ];
    }

    public function forUpdate(PedagogicalRecord $pedagogicalRecord, int $professionalId): array
    {
        $pedagogicalRecord->load([
            'attendanceSession.students.guardians.person',
            'attendanceSession.professional.person',
            'guardians.person',
        ]);
        if ((int) $pedagogicalRecord->attendanceSession->professional_id !== $professionalId) {
            throw new InvalidPedagogicalRecord('Apenas o profissional vinculado pode editar este registro pedagógico.');
        }

        return [
            'pedagogicalRecord' => $pedagogicalRecord,
            'context' => $this->context($pedagogicalRecord->attendanceSession),
        ];
    }

    /**
     * Dados de identificação exibidos pelo formulário, no mesmo formato do Blade legado.
     *
     * @return array<string, mixed>
     */
    private function context(Session $session): array
    {
        $student = $session->students->first();
        $guardians = $student?->guardians?->sortBy('person.name')->values() ?? collect();

        return [
            'session_id' => (int) $session->id,
            'session_objective' => $session->session_objective,
            'student_name' => $student?->person?->name,
            'professional_name' => $session->professional?->person?->name,
            'session_date' => $session->session_date?->format('d/m/Y'),
            'start_time' => Carbon::parse($session->start_time)->format('H:i'),
            'end_time' => $session->end_time ? Carbon::parse($session->end_time)->format('H:i') : null,
            'attendance_type_label' => AttendanceType::labelFor($session->attendance_type),
            'guardians' => $guardians
                ->map(fn (Guardian $guardian): array => [
                    'id' => (int) $guardian->id,
                    'name' => (string) $guardian->person?->name,
                    'relationship_label' => $guardian->relationshipLabel(),
                ])
                ->all(),
        ];
    }
}
