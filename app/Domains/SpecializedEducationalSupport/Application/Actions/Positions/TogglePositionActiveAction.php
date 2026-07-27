<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\Positions;

use App\Domains\SpecializedEducationalSupport\Application\Queries\Positions\PositionHasProfessionalsQuery;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Position;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class TogglePositionActiveAction
{
    public function __construct(
        private PositionHasProfessionalsQuery $hasProfessionals,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(Position $position): Position
    {
        return DB::transaction(function () use ($position): Position {
            $lockedPosition = Position::query()
                ->lockForUpdate()
                ->findOrFail($position->getKey());

            if ($lockedPosition->is_active) {
                $lockedPosition->ensureCanBeDeactivated(
                    $this->hasProfessionals->execute($lockedPosition),
                );
                $lockedPosition->deactivate();
            } else {
                $lockedPosition->activate();
            }

            $lockedPosition->save();

            return $lockedPosition;
        });
    }
}
