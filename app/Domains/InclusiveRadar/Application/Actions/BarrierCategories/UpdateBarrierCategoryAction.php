<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Actions\BarrierCategories;

use App\Domains\InclusiveRadar\Application\Data\BarrierCategories\UpdateBarrierCategoryData;
use App\Domains\InclusiveRadar\Domain\DTOs\BarrierCategories\UpdateBarrierCategoryDTO;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidBarrierCategory;
use App\Domains\InclusiveRadar\Domain\Models\BarrierCategory;

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
