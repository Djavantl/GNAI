<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Queries\AccessibleEducationalMaterials;

use App\Domains\InclusiveRadar\Domain\Models\AccessibleEducationalMaterial;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

final class ShowAccessibleEducationalMaterialQuery
{
    public function execute(AccessibleEducationalMaterial $material): AccessibleEducationalMaterial
    {
        return $material->load([
            'deficiencies' => static function (BelongsToMany $query): void {
                $query->orderBy('name');
            },
            'accessibilityFeatures' => static function (BelongsToMany $query): void {
                $query->orderBy('name');
            },
            'inspections' => static function (MorphMany $query): void {
                $query
                    ->with('images')
                    ->orderByDesc('inspection_date')
                    ->orderByDesc('created_at');
            },
            'loans',
        ]);
    }
}
