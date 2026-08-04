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
                'attendanceSession.students.currentCourse.course.disciplines',
            ])->lockForUpdate()->findOrFail($pedagogicalRecord->getKey());
            if ((int) $record->attendanceSession->professional_id !== $professionalId) {
                throw new InvalidPedagogicalRecord('Apenas o profissional vinculado ao agendamento pode editar o registro pedagógico.');
            }
            $record->revise(new PedagogicalRecordDTO(
                followUpReason: $data->followUpReason,
                followUpStatus: $data->followUpStatus,
                duration: $data->duration,
                isPresent: $data->isPresent,
                absenceReason: $data->absenceReason,
                systematicPedagogicalFollowUpRecord: $data->systematicPedagogicalFollowUpRecord,
                strategiesAndResourcesAdopted: $data->strategiesAndResourcesAdopted,
                schoolAttendanceStatus: $data->schoolAttendanceStatus,
                referralsMade: $data->referralsMade,
                complementaryObservations: $data->complementaryObservations,
            ));
            $this->ensureDisciplinesBelongToCurrentCourse(
                $record,
                $data->isPresent ? $data->failedDisciplineIds : [],
                $data->isPresent ? $data->atRiskDisciplineIds : [],
            );
            $record->save();
            $record->syncFailedDisciplines($data->isPresent ? $data->failedDisciplineIds : []);
            $record->syncAtRiskDisciplines($data->isPresent ? $data->atRiskDisciplineIds : []);

            return $record->fresh([
                'attendanceSession.students.person',
                'attendanceSession.professional.person',
                'failedDisciplines',
                'atRiskDisciplines',
            ]);
        });
    }

    /**
     * @param  list<int>  $failedDisciplineIds
     * @param  list<int>  $atRiskDisciplineIds
     */
    private function ensureDisciplinesBelongToCurrentCourse(PedagogicalRecord $record, array $failedDisciplineIds, array $atRiskDisciplineIds): void
    {
        $allowedIds = $record->attendanceSession->students->first()?->currentCourse?->course?->disciplines
            ?->pluck('id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->all() ?? [];
        $selectedIds = array_map('intval', [...$failedDisciplineIds, ...$atRiskDisciplineIds]);

        if (array_diff($selectedIds, $allowedIds) !== []) {
            throw new InvalidPedagogicalRecord('As disciplinas selecionadas devem pertencer ao curso atual do estudante.');
        }
    }
}
