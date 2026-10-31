<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\Positions;

use App\Domains\Inclusion\Domain\Models\Position;

final class ShowPositionQuery
{
    public function execute(Position $position): Position
    {
        return $position->load([
            'permissions' => fn ($query) => $query->orderBy('name'),
        ]);
    }
}
