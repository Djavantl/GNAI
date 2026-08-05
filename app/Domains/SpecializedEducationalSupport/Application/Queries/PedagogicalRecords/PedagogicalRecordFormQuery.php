<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\PedagogicalRecords;

use App\Domains\SpecializedEducationalSupport\Domain\Enums\AttendanceType;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\SessionStatus;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidPedagogicalRecord;
use App\Domains\SpecializedEducationalSupport\Domain\Models\PedagogicalRecord;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Session;

final class PedagogicalRecordFormQuery
{
    public function forCreation(Session $session, int $professionalId): array
    {
        $session->load([
            'students.person',
            'professional.person',
            'aeeRecord',
            'pedagogicalRecord',
        ]);
        if ((int) $session->professional_id !== $professionalId || ! AttendanceType::isPedagogical($session->attendance_type) || ! SessionStatus::isScheduledValue($session->status) || $session->students->count() !== 1 || $session->aeeRecord !== null || $session->pedagogicalRecord !== null) {
            throw new InvalidPedagogicalRecord('Este agendamento não está disponível para criação de registro pedagógico.');
        }

        return compact('session');
    }

    public function forUpdate(PedagogicalRecord $pedagogicalRecord, int $professionalId): array
    {
        $pedagogicalRecord->load([
            'attendanceSession.students.person',
            'attendanceSession.professional.person',
        ]);
        if ((int) $pedagogicalRecord->attendanceSession->professional_id !== $professionalId) {
            throw new InvalidPedagogicalRecord('Apenas o profissional vinculado pode editar este registro pedagógico.');
        }

        return compact('pedagogicalRecord');
    }
}
