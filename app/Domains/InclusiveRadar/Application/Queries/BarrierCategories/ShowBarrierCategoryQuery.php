<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Queries\BarrierCategories;

use App\Domains\InclusiveRadar\Domain\Models\BarrierCategory;

final class ShowBarrierCategoryQuery
{
    public function execute(BarrierCategory $category): BarrierCategory
    {
        return $category->loadCount('barriers');
    }
}
