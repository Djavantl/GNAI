<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Unit\Application\Data\Institutions;

use App\Domains\InclusiveRadar\Application\Data\Institutions\UpdateInstitutionData;
use Tests\TestCase;

final class UpdateInstitutionDataTest extends TestCase
{
    public function test_it_maps_snake_case_input(): void
    {
        $data = UpdateInstitutionData::from([
            'name' => 'IFBA Campus Guanambi',
            'short_name' => 'IFBA-GBI',
            'city' => 'Guanambi',
            'state' => 'Bahia',
            'latitude' => -14.22,
            'longitude' => -42.77,
            'default_zoom' => 16,
            'is_active' => false,
        ]);

        self::assertSame('IFBA Campus Guanambi', $data->name);
        self::assertSame('IFBA-GBI', $data->shortName);
        self::assertFalse($data->isActive);
    }
}
