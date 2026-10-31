<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\BarrierCategories;

use App\Domains\Inclusion\Domain\Models\BarrierCategory;

final class ShowBarrierCategoryQuery
{
    public function execute(BarrierCategory $category): BarrierCategory
    {
        return $category->loadCount('barriers');
    }
}
