<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\AeeRecords;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidAeeRecord;
use App\Domains\SpecializedEducationalSupport\Domain\Models\AeeRecord;

final class ShowAeeRecordQuery
{
    public function execute(AeeRecord $aeeRecord, User $user): AeeRecord
    {
        $aeeRecord->load(['attendanceSession.professional.person', 'attendanceSession.students.person', 'studentEvaluations.student.person']);
        if (! $user->can('aee-record.view-all') && (int) $aeeRecord->attendanceSession->professional_id !== (int) $user->professional_id) {
            throw new InvalidAeeRecord('Você não possui permissão para visualizar este registro AEE.');
        }

        return $aeeRecord;
    }
}
