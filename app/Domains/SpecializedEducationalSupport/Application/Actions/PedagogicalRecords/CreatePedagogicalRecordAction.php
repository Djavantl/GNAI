<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\PedagogicalRecords;

use App\Domains\SpecializedEducationalSupport\Application\Data\PedagogicalRecords\CreatePedagogicalRecordData;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\PedagogicalRecords\PedagogicalRecordDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\AttendanceType;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\SessionStatus;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidPedagogicalRecord;
use App\Domains\SpecializedEducationalSupport\Domain\Models\PedagogicalRecord;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Session;
use Illuminate\Support\Facades\DB;
use Throwable;

final class CreatePedagogicalRecordAction
{
    /** @throws Throwable */
    public function execute(CreatePedagogicalRecordData $data, int $professionalId): PedagogicalRecord
    {
        return DB::transaction(function () use ($data, $professionalId): PedagogicalRecord {
            $session = Session::query()->with([
                'students.guardians.person',
                'aeeRecord',
                'pedagogicalRecord',
            ])->lockForUpdate()->findOrFail($data->attendanceSessionId);
            if ((int) $session->professional_id !== $professionalId) {
                throw new InvalidPedagogicalRecord('Apenas o profissional vinculado ao agendamento pode criar o registro pedagógico.');
            }
            if (! SessionStatus::isScheduledValue($session->status) || ! AttendanceType::isPedagogical($session->attendance_type)) {
                throw new InvalidPedagogicalRecord('O agendamento deve estar agendado e classificado como Atendimento Pedagógico.');
            }
            if (! $session->sessionDateHasArrived()) {
                throw new InvalidPedagogicalRecord('O registro pedagógico só pode ser criado quando a data do agendamento chegar.');
            }
            if ($session->students->count() !== 1 || $session->aeeRecord !== null || $session->pedagogicalRecord !== null) {
                throw new InvalidPedagogicalRecord('O agendamento não está disponível para receber um registro pedagógico.');
            }
            $guardianIds = $data->withGuardians ? $data->guardianIds : [];
            $this->ensureGuardiansBelongToStudent($session, $guardianIds);
            $record = PedagogicalRecord::register($session, $this->dto($data));
            $record->save();
            $record->syncGuardians($guardianIds);
            $session->update(['status' => SessionStatus::COMPLETED_DATABASE_VALUE]);

            return $record->load([
                'attendanceSession.students.person',
                'attendanceSession.professional.person',
                'guardians.person',
            ]);
        });
    }

    private function dto(CreatePedagogicalRecordData $data): PedagogicalRecordDTO
    {
        return new PedagogicalRecordDTO(
            followUpReason: $data->followUpReason,
            duration: $data->duration,
            isPresent: $data->isPresent,
            absenceReason: $data->absenceReason,
            systematicPedagogicalFollowUpRecord: $data->systematicPedagogicalFollowUpRecord,
            strategiesAndResourcesAdopted: $data->strategiesAndResourcesAdopted,
            referralsMade: $data->referralsMade,
            complementaryObservations: $data->complementaryObservations,
        );
    }

    /** @param list<int> $guardianIds */
    private function ensureGuardiansBelongToStudent(Session $session, array $guardianIds): void
    {
        $allowedIds = $session->students->first()?->guardians
            ->pluck('id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->all() ?? [];

        if (array_diff(array_map('intval', $guardianIds), $allowedIds) !== []) {
            throw new InvalidPedagogicalRecord('Os responsáveis selecionados devem estar vinculados ao estudante do atendimento.');
        }
    }
}
