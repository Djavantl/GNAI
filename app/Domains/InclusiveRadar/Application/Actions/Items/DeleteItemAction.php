<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Actions\Items;

use App\Domains\InclusiveRadar\Domain\Exceptions\ResourceHasOpenLoans;
use App\Domains\InclusiveRadar\Domain\Models\Item;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class DeleteItemAction
{
    /**
     * @throws Throwable
     */
    public function execute(Item $item): void
    {
        DB::transaction(function () use ($item): void {
            $lockedItem = Item::query()
                ->lockForUpdate()
                ->findOrFail($item->getKey());

            $hasOpenLoans = $lockedItem->loans()
                ->whereNull('return_date')
                ->exists();

            if ($hasOpenLoans) {
                throw new ResourceHasOpenLoans;
            }

            $lockedItem->delete();
        });
    }
}
