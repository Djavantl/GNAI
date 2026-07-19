<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Queries\AccessibleEducationalMaterials;

use App\Domains\InclusiveRadar\Domain\Models\AccessibleEducationalMaterial;
use App\Domains\InclusiveRadar\Domain\ValueObjects\AssetCode;
use Illuminate\Database\Eloquent\Builder;

final class AccessibleEducationalMaterialAssetCodeExistsQuery
{
    public function execute(AssetCode $assetCode, ?int $ignoreId = null): bool
    {
        return AccessibleEducationalMaterial::query()
            ->where('asset_code', $assetCode->value())
            ->when(
                $ignoreId !== null,
                static fn (Builder $query): Builder => $query->whereKeyNot($ignoreId),
            )
            ->exists();
    }
}
