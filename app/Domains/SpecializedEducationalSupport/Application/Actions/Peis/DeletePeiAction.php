<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\Peis;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Pei;
use Illuminate\Support\Facades\DB;
use Throwable;

final class DeletePeiAction
{
    /**
     * @throws Throwable
     */
    public function execute(Pei $pei, User $user): int
    {
        return DB::transaction(function () use ($pei, $user): int {
            $lockedPei = Pei::query()
                ->lockForUpdate()
                ->findOrFail($pei->getKey());
            $lockedPei->ensureCanBeManagedBy((int) $user->getKey());

            $studentId = (int) $lockedPei->student_id;
            $lockedPei->delete();

            return $studentId;
        });
    }
}
