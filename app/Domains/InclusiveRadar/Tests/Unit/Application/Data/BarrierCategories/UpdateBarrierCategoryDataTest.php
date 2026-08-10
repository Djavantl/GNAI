<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Unit\Application\Data\BarrierCategories;

use App\Domains\InclusiveRadar\Application\Data\BarrierCategories\UpdateBarrierCategoryData;
use Tests\TestCase;

final class UpdateBarrierCategoryDataTest extends TestCase
{
    public function test_it_maps_snake_case_input(): void
    {
        $data = UpdateBarrierCategoryData::from([
            'name' => 'Comunicacional',
            'description' => 'Barreiras de comunicação.',
            'blocks_map' => true,
            'is_active' => false,
        ]);

        self::assertSame('Comunicacional', $data->name);
        self::assertSame('Barreiras de comunicação.', $data->description);
        self::assertTrue($data->blocksMap);
        self::assertFalse($data->isActive);
    }
}
