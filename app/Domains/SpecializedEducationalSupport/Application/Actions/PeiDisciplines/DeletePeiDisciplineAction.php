<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\PeiDisciplines;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidPei;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidPeiDiscipline;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Pei;
use App\Domains\SpecializedEducationalSupport\Domain\Models\PeiDiscipline;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class DeletePeiDisciplineAction
{
    /**
     * @throws InvalidPei
     * @throws InvalidPeiDiscipline
     * @throws ModelNotFoundException
     * @throws Throwable
     */
    public function execute(PeiDiscipline $peiDiscipline, User $user): void
    {
        DB::transaction(function () use ($peiDiscipline, $user): void {
            $lockedPei = Pei::query()
                ->lockForUpdate()
                ->findOrFail($peiDiscipline->pei_id);

            $lockedPeiDiscipline = PeiDiscipline::query()
                ->lockForUpdate()
                ->where('pei_id', $lockedPei->getKey())
                ->findOrFail($peiDiscipline->getKey());

            $lockedPeiDiscipline->ensureCanBeManagedBy((int) $user->getKey());

            if ($lockedPei->is_finished) {
                throw new InvalidPei('Este PEI já foi finalizado e não permite alterações.');
            }

            $lockedPeiDiscipline->delete();
        });
    }
}
