<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Queries\AccessibleEducationalMaterials;

use App\Domains\InclusiveRadar\Application\Data\AccessibleEducationalMaterials\ListAccessibleEducationalMaterialsData;
use App\Domains\InclusiveRadar\Domain\Models\AccessibleEducationalMaterial;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ListAccessibleEducationalMaterialsQuery
{
    /**
     * @return LengthAwarePaginator<int, AccessibleEducationalMaterial>
     */
    public function execute(ListAccessibleEducationalMaterialsData $filters): LengthAwarePaginator
    {
        $query = AccessibleEducationalMaterial::query()
            ->with(['deficiencies', 'accessibilityFeatures']);

        if (filled($filters->name)) {
            $query->where('name', 'like', '%'.trim((string) $filters->name).'%');
        }

        if ($filters->status !== null) {
            $query->where('status', $filters->status->value);
        }

        if ($filters->isDigital !== null) {
            $query->where('is_digital', $filters->isDigital);
        }

        if ($filters->isActive !== null) {
            $query->where('is_active', $filters->isActive);
        }

        if ($filters->available === true) {
            $query->where('quantity_available', '>', 0);
        }

        if ($filters->available === false) {
            $query->where('quantity_available', '<=', 0);
        }

        return $query
            ->orderBy('name')
            ->paginate($filters->perPage)
            ->withQueryString();
    }
}
