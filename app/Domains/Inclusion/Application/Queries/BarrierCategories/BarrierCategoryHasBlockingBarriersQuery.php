<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\BarrierCategories;

use App\Domains\Inclusion\Domain\Enums\BarrierStatus;
use App\Domains\Inclusion\Domain\Models\Barrier;
use App\Domains\Inclusion\Domain\Models\BarrierCategory;

final class BarrierCategoryHasBlockingBarriersQuery
{
    public function execute(BarrierCategory $category): bool
    {
        $latestStatus = <<<'SQL'
            COALESCE((
                SELECT inspections.status
                FROM inspections
                WHERE inspections.inspectable_id = barriers.id
                    AND inspections.inspectable_type = ?
                ORDER BY inspections.inspection_date DESC, inspections.created_at DESC
                LIMIT 1
            ), '')
        SQL;

        return $category
            ->barriers()
            ->whereRaw(
                "{$latestStatus} NOT IN (?, ?)",
                [
                    (new Barrier())->getMorphClass(),
                    BarrierStatus::RESOLVED->value,
                    BarrierStatus::NOT_APPLICABLE->value,
                ],
            )
            ->exists();
    }
}
