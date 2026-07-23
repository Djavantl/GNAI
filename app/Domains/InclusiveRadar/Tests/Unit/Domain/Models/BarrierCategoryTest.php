<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Unit\Domain\Models;

use App\Domains\InclusiveRadar\Domain\DTOs\BarrierCategories\CreateBarrierCategoryDTO;
use App\Domains\InclusiveRadar\Domain\DTOs\BarrierCategories\UpdateBarrierCategoryDTO;
use App\Domains\InclusiveRadar\Domain\Models\BarrierCategory;
use Tests\TestCase;

final class BarrierCategoryTest extends TestCase
{
    public function test_it_registers_a_category(): void
    {
        $category = BarrierCategory::register(new CreateBarrierCategoryDTO(
            name: ' Arquitetônica ',
            description: ' Barreiras físicas. ',
            blocksMap: false,
            isActive: true,
        ));

        self::assertSame('Arquitetônica', $category->name);
        self::assertSame('Barreiras físicas.', $category->description);
        self::assertFalse($category->blocks_map);
        self::assertTrue($category->is_active);
    }

    public function test_it_revises_a_category(): void
    {
        $category = new BarrierCategory([
            'name' => 'Arquitetônica',
            'description' => 'Antiga.',
            'blocks_map' => true,
            'is_active' => true,
        ]);

        $category->revise(new UpdateBarrierCategoryDTO(
            name: 'Comunicacional',
            description: '',
            blocksMap: false,
            isActive: false,
        ));

        self::assertSame('Comunicacional', $category->name);
        self::assertNull($category->description);
        self::assertFalse($category->blocks_map);
        self::assertFalse($category->is_active);
    }

    public function test_it_trims_name(): void
    {
        $category = BarrierCategory::register(new CreateBarrierCategoryDTO(
            name: ' Comunicacional ',
        ));

        self::assertSame('Comunicacional', $category->name);
    }

}
