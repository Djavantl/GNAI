<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\Positions;

use App\Domains\Inclusion\Domain\Models\Position;

final class PositionHasProfessionalsQuery
{
    public function execute(Position $position): bool
    {
        return $position->professionals()->exists();
    }
}
