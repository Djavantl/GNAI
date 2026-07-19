<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Queries\AssistiveTechnologies;

use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;
use App\Domains\InclusiveRadar\Domain\ValueObjects\AssetCode;

final class AssetCodeExistsQuery
{
    public function execute(AssetCode $assetCode, ?int $ignoreId = null): bool
    {
        return AssistiveTechnology::query()
            ->where('asset_code', $assetCode->value())
            ->when(
                $ignoreId !== null,
                static fn ($query) => $query->whereKeyNot($ignoreId),
            )
            ->exists();
    }
}
