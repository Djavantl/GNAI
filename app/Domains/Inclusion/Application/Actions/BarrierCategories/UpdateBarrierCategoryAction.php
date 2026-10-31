<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\BarrierCategories;

use App\Domains\Inclusion\Application\Data\BarrierCategories\UpdateBarrierCategoryData;
use App\Domains\Inclusion\Domain\DTOs\BarrierCategories\UpdateBarrierCategoryDTO;
use App\Domains\Inclusion\Domain\Exceptions\InvalidBarrierCategory;
use App\Domains\Inclusion\Domain\Models\BarrierCategory;

final readonly class UpdateBarrierCategoryAction
{
    /**
     * @throws InvalidBarrierCategory
     */
    public function execute(BarrierCategory $category, UpdateBarrierCategoryData $data): BarrierCategory
    {
        $categoryDTO = new UpdateBarrierCategoryDTO(
            name: $data->name,
            description: $data->description,
            blocksMap: $data->blocksMap,
            isActive: $data->isActive,
        );

        $category->revise($categoryDTO);
        $category->save();

        return $category;
    }
}
