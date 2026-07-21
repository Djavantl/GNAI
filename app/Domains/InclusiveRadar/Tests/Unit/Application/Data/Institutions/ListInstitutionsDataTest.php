<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Unit\Application\Data\Institutions;

use App\Domains\InclusiveRadar\Application\Data\Institutions\ListInstitutionsData;
use Tests\TestCase;

final class ListInstitutionsDataTest extends TestCase
{
    public function test_it_maps_filters_and_preserves_false_values(): void
    {
        $data = ListInstitutionsData::from([
            'name' => 'IFBA',
            'location' => 'Guanambi',
            'is_active' => false,
            'per_page' => 25,
        ]);

        self::assertSame('IFBA', $data->name);
        self::assertSame('Guanambi', $data->location);
        self::assertFalse($data->isActive);
        self::assertSame(25, $data->perPage);
    }

    public function test_it_applies_default_filters(): void
    {
        $data = ListInstitutionsData::from([]);

        self::assertNull($data->name);
        self::assertNull($data->location);
        self::assertNull($data->isActive);
        self::assertSame(10, $data->perPage);
    }
}
