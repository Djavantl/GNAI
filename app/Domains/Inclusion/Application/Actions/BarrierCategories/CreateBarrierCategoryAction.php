<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\BarrierCategories;

use App\Domains\Inclusion\Application\Data\BarrierCategories\CreateBarrierCategoryData;
use App\Domains\Inclusion\Domain\DTOs\BarrierCategories\CreateBarrierCategoryDTO;
use App\Domains\Inclusion\Domain\Exceptions\InvalidBarrierCategory;
use App\Domains\Inclusion\Domain\Models\BarrierCategory;

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
            isActive: $data->isActive,
        );

        $category = BarrierCategory::register($categoryDTO);
        $category->save();

        return $category;
    }
}
