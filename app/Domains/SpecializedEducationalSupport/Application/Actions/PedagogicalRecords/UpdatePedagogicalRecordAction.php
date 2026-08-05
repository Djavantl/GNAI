<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\PedagogicalRecords;

use App\Domains\SpecializedEducationalSupport\Application\Data\PedagogicalRecords\UpdatePedagogicalRecordData;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\PedagogicalRecords\PedagogicalRecordDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidPedagogicalRecord;
use App\Domains\SpecializedEducationalSupport\Domain\Models\PedagogicalRecord;
use Illuminate\Support\Facades\DB;
use Throwable;

final class UpdatePedagogicalRecordAction
{
    /** @throws Throwable */
    public function execute(PedagogicalRecord $pedagogicalRecord, UpdatePedagogicalRecordData $data, int $professionalId): PedagogicalRecord
    {
        return DB::transaction(function () use ($pedagogicalRecord, $data, $professionalId): PedagogicalRecord {
            $record = PedagogicalRecord::query()->with('attendanceSession')->lockForUpdate()->findOrFail($pedagogicalRecord->getKey());
            if ((int) $record->attendanceSession->professional_id !== $professionalId) {
                throw new InvalidPedagogicalRecord('Apenas o profissional vinculado ao agendamento pode editar o registro pedagógico.');
            }
            $record->revise(new PedagogicalRecordDTO(
                followUpReason: $data->followUpReason,
                duration: $data->duration,
                isPresent: $data->isPresent,
                absenceReason: $data->absenceReason,
                systematicPedagogicalFollowUpRecord: $data->systematicPedagogicalFollowUpRecord,
                strategiesAndResourcesAdopted: $data->strategiesAndResourcesAdopted,
                referralsMade: $data->referralsMade,
                complementaryObservations: $data->complementaryObservations,
            ));
            $record->save();

            return $record->fresh([
                'attendanceSession.students.person',
                'attendanceSession.professional.person',
            ]);
        });
    }
}
