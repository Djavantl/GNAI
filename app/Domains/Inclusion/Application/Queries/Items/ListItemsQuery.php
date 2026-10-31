<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\Items;

use App\Domains\Inclusion\Application\Data\Items\ListItemsData;
use App\Domains\Inclusion\Domain\Models\Item;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ListItemsQuery
{
    /**
     * @return LengthAwarePaginator<int, Item>
     */
    public function execute(ListItemsData $filters): LengthAwarePaginator
    {
        $query = Item::query()
            ->with(['deficiencies', 'accessibilityFeatures']);

        if (filled($filters->name)) {
            $query->where('name', 'like', '%'.trim($filters->name).'%');
        }

        if ($filters->itemType !== null) {
            $query->where('item_type', $filters->itemType->value);
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

        if ($filters->accessibilityFeatureId !== null) {
            $query->whereHas(
                'accessibilityFeatures',
                static fn ($featureQuery) => $featureQuery->where(
                    'accessibility_features.id',
                    $filters->accessibilityFeatureId,
                ),
            );
        }

        return $query
            ->orderBy('name')
            ->paginate($filters->perPage)
            ->withQueryString();
    }
}
