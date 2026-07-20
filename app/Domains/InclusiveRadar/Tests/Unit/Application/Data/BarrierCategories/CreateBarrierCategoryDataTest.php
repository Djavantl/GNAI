<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Unit\Application\Data\BarrierCategories;

use App\Domains\InclusiveRadar\Application\Data\BarrierCategories\CreateBarrierCategoryData;
use Tests\TestCase;

final class CreateBarrierCategoryDataTest extends TestCase
{
    public function test_it_maps_snake_case_input(): void
    {
        $data = CreateBarrierCategoryData::from([
            'name' => 'Arquitetônica',
            'description' => 'Barreiras físicas.',
            'blocks_map' => false,
            'is_active' => true,
        ]);

        self::assertSame('Arquitetônica', $data->name);
        self::assertSame('Barreiras físicas.', $data->description);
        self::assertFalse($data->blocksMap);
        self::assertTrue($data->isActive);
    }

    public function test_it_applies_checkbox_defaults(): void
    {
        $data = CreateBarrierCategoryData::from([
            'name' => 'Atitudinal',
        ]);

        self::assertFalse($data->blocksMap);
        self::assertFalse($data->isActive);
    }
}
