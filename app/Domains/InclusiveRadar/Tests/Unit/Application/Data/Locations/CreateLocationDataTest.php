<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Unit\Application\Data\Locations;

use App\Domains\InclusiveRadar\Application\Data\Locations\CreateLocationData;
use Tests\TestCase;

final class CreateLocationDataTest extends TestCase
{
    public function test_it_maps_snake_case_input(): void
    {
        $data = CreateLocationData::from([
            'institution_id' => 10,
            'name' => 'Biblioteca',
            'latitude' => -14.22,
            'longitude' => -42.78,
            'google_place_id' => 'place-123',
            'is_active' => true,
        ]);

        self::assertSame(10, $data->institutionId);
        self::assertSame('Biblioteca', $data->name);
        self::assertSame(-14.22, $data->latitude);
        self::assertSame(-42.78, $data->longitude);
        self::assertSame('place-123', $data->googlePlaceId);
        self::assertTrue($data->isActive);
    }

    public function test_it_applies_checkbox_defaults(): void
    {
        $data = CreateLocationData::from([
            'institution_id' => 10,
            'name' => 'Biblioteca',
            'latitude' => -14.22,
            'longitude' => -42.78,
        ]);

        self::assertFalse($data->isActive);
    }
}
