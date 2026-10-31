<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\Items;

use App\Domains\Inclusion\Domain\Models\Item;
use App\Domains\Inclusion\Domain\Models\Inspection;
use Illuminate\Auth\Access\AuthorizationException;

final class ShowItemInspectionQuery
{
    /**
     * @throws AuthorizationException
     */
    public function execute(
        Item $item,
        Inspection $inspection,
    ): Inspection {
        $scopedInspection = $item->inspections()
            ->with('evidences')
            ->whereKey($inspection->getKey())
            ->first();

        if ($scopedInspection === null) {
            throw new AuthorizationException(
                'A inspeção não pertence ao item informado.'
            );
        }

        return $scopedInspection;
    }
}
