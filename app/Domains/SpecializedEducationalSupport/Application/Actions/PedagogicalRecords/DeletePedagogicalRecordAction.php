<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\PedagogicalRecords;

use App\Domains\SpecializedEducationalSupport\Domain\Enums\SessionStatus;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidPedagogicalRecord;
use App\Domains\SpecializedEducationalSupport\Domain\Models\PedagogicalRecord;
use Illuminate\Support\Facades\DB;
use Throwable;

final class DeletePedagogicalRecordAction
{
    /** @throws Throwable */
    public function execute(PedagogicalRecord $pedagogicalRecord, int $professionalId): int
    {
        return DB::transaction(function () use ($pedagogicalRecord, $professionalId): int {
            $record = PedagogicalRecord::query()->with('attendanceSession')->lockForUpdate()->findOrFail($pedagogicalRecord->getKey());
            if ((int) $record->attendanceSession->professional_id !== $professionalId) {
                throw new InvalidPedagogicalRecord('Apenas o profissional vinculado ao agendamento pode excluir o registro pedagógico.');
            }
            $session = $record->attendanceSession;
            $record->forceDelete();
            $session->update(['status' => SessionStatus::SCHEDULED_DATABASE_VALUE]);

            return (int) $session->getKey();
        });
    }
}
