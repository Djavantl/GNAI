<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Queries\AccessibleEducationalMaterials;

use App\Domains\InclusiveRadar\Domain\Models\AccessibleEducationalMaterial;
use App\Domains\InclusiveRadar\Domain\Models\Inspection;
use Illuminate\Auth\Access\AuthorizationException;

final class ShowAccessibleEducationalMaterialInspectionQuery
{
    /**
     * @throws AuthorizationException
     */
    public function execute(
        AccessibleEducationalMaterial $material,
        Inspection $inspection,
    ): Inspection {
        $scopedInspection = $material->inspections()
            ->with('evidences')
            ->whereKey($inspection->getKey())
            ->first();

        if ($scopedInspection === null) {
            throw new AuthorizationException(
                'A inspeção não pertence ao material pedagógico informado.'
            );
        }

        return $scopedInspection;
    }
}
