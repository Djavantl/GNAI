<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Unit\Application\Data\Institutions;

use App\Domains\InclusiveRadar\Application\Data\Institutions\CreateInstitutionData;
use Tests\TestCase;

final class CreateInstitutionDataTest extends TestCase
{
    public function test_it_maps_snake_case_input(): void
    {
        $data = CreateInstitutionData::from([
            'name' => 'IFBA Campus Guanambi',
            'short_name' => 'IFBA-GBI',
            'city' => 'Guanambi',
            'state' => 'Bahia',
            'district' => 'Centro',
            'address' => 'Rua A',
            'latitude' => -14.22,
            'longitude' => -42.77,
            'default_zoom' => 16,
            'is_active' => true,
        ]);

        self::assertSame('IFBA Campus Guanambi', $data->name);
        self::assertSame('IFBA-GBI', $data->shortName);
        self::assertSame('Guanambi', $data->city);
        self::assertSame('Bahia', $data->state);
        self::assertSame('Centro', $data->district);
        self::assertSame('Rua A', $data->address);
        self::assertSame(-14.22, $data->latitude);
        self::assertSame(-42.77, $data->longitude);
        self::assertSame(16, $data->defaultZoom);
        self::assertTrue($data->isActive);
    }

    public function test_it_applies_checkbox_defaults(): void
    {
        $data = CreateInstitutionData::from([
            'name' => 'IFBA',
            'city' => 'Guanambi',
            'state' => 'Bahia',
            'latitude' => -14.22,
            'longitude' => -42.77,
        ]);

        self::assertFalse($data->isActive);
    }
}
