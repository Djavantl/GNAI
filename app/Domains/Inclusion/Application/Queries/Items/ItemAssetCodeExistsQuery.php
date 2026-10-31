<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\Items;

use App\Domains\Inclusion\Domain\Models\Item;
use App\Domains\Inclusion\Domain\ValueObjects\AssetCode;
use Illuminate\Database\Eloquent\Builder;

final class ItemAssetCodeExistsQuery
{
    public function execute(AssetCode $assetCode, ?int $ignoreId = null): bool
    {
        return Item::query()
            ->where('asset_code', $assetCode->value())
            ->when(
                $ignoreId !== null,
                static fn (Builder $query): Builder => $query->whereKeyNot($ignoreId),
            )
            ->exists();
    }
}
