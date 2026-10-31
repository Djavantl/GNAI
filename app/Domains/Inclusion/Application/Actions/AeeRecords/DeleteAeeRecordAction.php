<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\AeeRecords;

use App\Domains\Inclusion\Domain\Enums\SessionStatus;
use App\Domains\Inclusion\Domain\Exceptions\InvalidAeeRecord;
use App\Domains\Inclusion\Domain\Models\AeeRecord;
use App\Domains\Inclusion\Domain\Models\Session;
use Illuminate\Support\Facades\DB;
use Throwable;

final class DeleteAeeRecordAction
{
    /** @throws Throwable */
    public function execute(AeeRecord $aeeRecord, int $professionalId): int
    {
        return DB::transaction(function () use ($aeeRecord, $professionalId): int {
            $record = AeeRecord::query()->lockForUpdate()->findOrFail($aeeRecord->getKey());
            $session = Session::query()->lockForUpdate()->findOrFail($record->attendance_session_id);
            if ((int) $session->professional_id !== $professionalId) {
                throw new InvalidAeeRecord('Apenas o profissional vinculado ao agendamento pode excluir o registro AEE.');
            }
            $record->forceDelete();
            $session->update(['status' => SessionStatus::SCHEDULED_DATABASE_VALUE]);

            return (int) $session->getKey();
        });
    }
}
