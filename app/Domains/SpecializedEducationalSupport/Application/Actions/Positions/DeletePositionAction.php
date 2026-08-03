<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\Positions;

use App\Domains\SpecializedEducationalSupport\Application\Queries\Positions\PositionHasProfessionalsQuery;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Position;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class DeletePositionAction
{
    public function __construct(
        private PositionHasProfessionalsQuery $hasProfessionals,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(Position $position): void
    {
        DB::transaction(function () use ($position): void {
            $lockedPosition = Position::query()
                ->lockForUpdate()
                ->findOrFail($position->getKey());

            $lockedPosition->ensureCanBeDeleted(
                $this->hasProfessionals->execute($lockedPosition),
            );

            $lockedPosition->delete();
        });
    }
}
