<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Queries\Barriers;

use App\Domains\InclusiveRadar\Domain\Models\Barrier;
use App\Domains\InclusiveRadar\Domain\Models\Inspection;
use Illuminate\Auth\Access\AuthorizationException;

final class ShowBarrierInspectionQuery
{
    /**
     * @throws AuthorizationException
     */
    public function execute(Barrier $barrier, Inspection $inspection): Inspection
    {
        $scopedInspection = $barrier->inspections()
            ->with('evidences')
            ->whereKey($inspection->getKey())
            ->first();

        if ($scopedInspection === null) {
            throw new AuthorizationException(
                'A inspeção não pertence à barreira informada.'
            );
        }

        return $scopedInspection;
    }
}
