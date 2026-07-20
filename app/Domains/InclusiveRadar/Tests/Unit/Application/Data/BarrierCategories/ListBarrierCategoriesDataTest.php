<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Unit\Application\Data\BarrierCategories;

use App\Domains\InclusiveRadar\Application\Data\BarrierCategories\ListBarrierCategoriesData;
use Tests\TestCase;

final class ListBarrierCategoriesDataTest extends TestCase
{
    public function test_it_maps_filters_and_preserves_false_values(): void
    {
        $data = ListBarrierCategoriesData::from([
            'name' => 'Arquitetônica',
            'is_active' => false,
            'per_page' => 25,
        ]);

        self::assertSame('Arquitetônica', $data->name);
        self::assertFalse($data->isActive);
        self::assertSame(25, $data->perPage);
    }

    public function test_it_applies_default_filters(): void
    {
        $data = ListBarrierCategoriesData::from([]);

        self::assertNull($data->name);
        self::assertNull($data->isActive);
        self::assertSame(10, $data->perPage);
    }
}
