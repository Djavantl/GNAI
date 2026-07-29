<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Queries\AssistiveTechnologies;

use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;
use App\Domains\InclusiveRadar\Domain\Models\Inspection;
use Illuminate\Auth\Access\AuthorizationException;

final class ShowAssistiveTechnologyInspectionQuery
{
    /**
     * @throws AuthorizationException
     */
    public function execute(
        AssistiveTechnology $technology,
        Inspection $inspection,
    ): Inspection {
        $scopedInspection = $technology->inspections()
            ->with('evidences')
            ->whereKey($inspection->getKey())
            ->first();

        if ($scopedInspection === null) {
            throw new AuthorizationException(
                'A inspeção não pertence à tecnologia assistiva informada.'
            );
        }

        return $scopedInspection;
    }
}
