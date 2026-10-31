<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\Items;

use App\Domains\Inclusion\Domain\Models\Item;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

final class ItemPdfQuery
{
    public function execute(Item $item): Item
    {
        return $item->load([
            'deficiencies' => static function (BelongsToMany $query): void {
                $query->orderBy('name');
            },
            'accessibilityFeatures' => static function (BelongsToMany $query): void {
                $query->orderBy('name');
            },
            'inspections' => static function (MorphMany $query): void {
                $query
                    ->with('evidences')
                    ->orderByDesc('inspection_date')
                    ->orderByDesc('created_at');
            },
        ]);
    }
}
