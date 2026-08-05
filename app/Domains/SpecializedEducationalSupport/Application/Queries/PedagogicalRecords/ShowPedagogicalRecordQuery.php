<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\PedagogicalRecords;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidPedagogicalRecord;
use App\Domains\SpecializedEducationalSupport\Domain\Models\PedagogicalRecord;

final class ShowPedagogicalRecordQuery
{
    public function execute(PedagogicalRecord $pedagogicalRecord, User $user): PedagogicalRecord
    {
        $pedagogicalRecord->load([
            'attendanceSession.students.person',
            'attendanceSession.professional.person',
        ]);
        if (! $user->can('pedagogical-record.view-all') && (int) $pedagogicalRecord->attendanceSession->professional_id !== (int) $user->professional_id) {
            throw new InvalidPedagogicalRecord('Você não possui permissão para visualizar este registro pedagógico.');
        }

        return $pedagogicalRecord;
    }
}
