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
            $record = PedagogicalRecord::query()->with([
                'attendanceSession.students.guardians.person',
            ])->lockForUpdate()->findOrFail($pedagogicalRecord->getKey());
            if ((int) $record->attendanceSession->professional_id !== $professionalId) {
                throw new InvalidPedagogicalRecord('Apenas o profissional vinculado ao agendamento pode editar o registro pedagógico.');
            }
            $guardianIds = $data->withGuardians ? $data->guardianIds : [];
            $this->ensureGuardiansBelongToStudent($record, $guardianIds);
            $record->revise(new PedagogicalRecordDTO(
                followUpReason: $data->followUpReason,
                duration: $data->duration,
                isPresent: $data->isPresent,
                absenceReason: $data->absenceReason,
                systematicPedagogicalFollowUpRecord: $data->systematicPedagogicalFollowUpRecord,
                strategiesAndResourcesAdopted: $data->strategiesAndResourcesAdopted,
                referralsMade: $data->referralsMade,
                complementaryObservations: $data->complementaryObservations,
                withGuardians: $data->withGuardians,
            ));
            $record->save();
            $record->syncGuardians($guardianIds);

            return $record->fresh([
                'attendanceSession.students.person',
                'attendanceSession.professional.person',
                'guardians.person',
            ]);
        });
    }

    /** @param list<int> $guardianIds */
    private function ensureGuardiansBelongToStudent(PedagogicalRecord $record, array $guardianIds): void
    {
        $allowedIds = $record->attendanceSession->students->first()?->guardians
            ->pluck('id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->all() ?? [];

        if (array_diff(array_map('intval', $guardianIds), $allowedIds) !== []) {
            throw new InvalidPedagogicalRecord('Os responsáveis selecionados devem estar vinculados ao estudante do atendimento.');
        }
    }
}
