<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Unit\Application\Data\Locations;

use App\Domains\InclusiveRadar\Application\Data\Locations\ListLocationsData;
use Tests\TestCase;

final class ListLocationsDataTest extends TestCase
{
    public function test_it_maps_snake_case_filters(): void
    {
        $data = ListLocationsData::from([
            'name' => 'Biblioteca',
            'institution_name' => 'Guanambi',
            'is_active' => true,
            'per_page' => 20,
        ]);

        self::assertSame('Biblioteca', $data->name);
        self::assertSame('Guanambi', $data->institutionName);
        self::assertTrue($data->isActive);
        self::assertSame(20, $data->perPage);
    }
}
