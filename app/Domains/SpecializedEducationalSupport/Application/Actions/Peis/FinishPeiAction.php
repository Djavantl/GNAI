<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\Peis;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Pei;
use Illuminate\Support\Facades\DB;
use Throwable;

final class FinishPeiAction
{
    /**
     * @throws Throwable
     */
    public function execute(Pei $pei, User $user): Pei
    {
        return DB::transaction(function () use ($pei, $user): Pei {
            $lockedPei = Pei::query()
                ->with(['peiDisciplines.discipline'])
                ->lockForUpdate()
                ->findOrFail($pei->getKey());
            $lockedPei->ensureCanBeManagedBy((int) $user->getKey());
            $lockedPei->ensureCanBeFinished();
            $lockedPei->markAsFinished();
            $lockedPei->save();

            return $lockedPei->load(['student.person', 'semester', 'course', 'studentContext']);
        });
    }
}
