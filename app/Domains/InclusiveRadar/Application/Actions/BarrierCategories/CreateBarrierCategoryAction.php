<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Actions\BarrierCategories;

use App\Domains\InclusiveRadar\Application\Data\BarrierCategories\CreateBarrierCategoryData;
use App\Domains\InclusiveRadar\Domain\DTOs\BarrierCategories\CreateBarrierCategoryDTO;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidBarrierCategory;
use App\Domains\InclusiveRadar\Domain\Models\BarrierCategory;

final readonly class CreateBarrierCategoryAction
{
    /**
     * @throws InvalidBarrierCategory
     */
    public function execute(CreateBarrierCategoryData $data): BarrierCategory
    {
        $categoryDTO = new CreateBarrierCategoryDTO(
            name: $data->name,
            description: $data->description,
            blocksMap: $data->blocksMap,
            active: $data->isActive,
        );

        $category = BarrierCategory::register($categoryDTO);
        $category->save();

        return $category;
    }
}
