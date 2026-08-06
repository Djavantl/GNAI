<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\AeeRecords;

use App\Domains\SpecializedEducationalSupport\Domain\Enums\AttendanceType;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\SessionStatus;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidAeeRecord;
use App\Domains\SpecializedEducationalSupport\Domain\Models\AeeRecord;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Session;

final class AeeRecordFormQuery
{
    public function forCreation(Session $session, int $professionalId): array
    {
        $session->load(['students.person', 'aeeRecord', 'pedagogicalRecord']);
        if ((int) $session->professional_id !== $professionalId || ! AttendanceType::isAee($session->attendance_type) || ! SessionStatus::isScheduledValue($session->status) || ! $session->sessionDateHasArrived() || $session->aeeRecord !== null || $session->pedagogicalRecord !== null) {
            throw new InvalidAeeRecord('Este agendamento não está disponível para criação de registro AEE.');
        }

        return compact('session');
    }

    public function forUpdate(AeeRecord $aeeRecord, int $professionalId): array
    {
        $aeeRecord->load(['studentEvaluations.student.person', 'attendanceSession.students.person']);
        if ((int) $aeeRecord->attendanceSession->professional_id !== $professionalId) {
            throw new InvalidAeeRecord('Apenas o profissional vinculado pode editar este registro AEE.');
        }

        return [
            'aeeRecord' => $aeeRecord,
            'session' => $aeeRecord->attendanceSession,
        ];
    }
}
