<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\Items;

use App\Domains\Inclusion\Domain\Models\AccessibilityFeature;

final class ItemFilterOptionsQuery
{
    /**
     * @return array<string, string>
     */
    public function execute(): array
    {
        return AccessibilityFeature::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->mapWithKeys(
                static fn (string $name, int $id): array => [(string) $id => $name],
            )
            ->all();
    }
}
