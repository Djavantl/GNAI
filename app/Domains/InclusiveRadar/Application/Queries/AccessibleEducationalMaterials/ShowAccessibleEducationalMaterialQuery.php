<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Queries\AccessibleEducationalMaterials;

use App\Domains\InclusiveRadar\Domain\Models\AccessibleEducationalMaterial;

final class ShowAccessibleEducationalMaterialQuery
{
    public function execute(AccessibleEducationalMaterial $material): AccessibleEducationalMaterial
    {
        return $material->load([
            'deficiencies' => static fn ($query) => $query->orderBy('name'),
            'accessibilityFeatures' => static fn ($query) => $query->orderBy('name'),
            'inspections' => static fn ($query) => $query
                ->with('images')
                ->orderByDesc('inspection_date')
                ->orderByDesc('created_at'),
            'loans',
        ]);
    }
}
