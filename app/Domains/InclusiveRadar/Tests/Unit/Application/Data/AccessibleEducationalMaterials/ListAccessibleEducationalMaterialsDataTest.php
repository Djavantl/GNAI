<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Unit\Application\Data\AccessibleEducationalMaterials;

use App\Domains\InclusiveRadar\Application\Data\AccessibleEducationalMaterials\ListAccessibleEducationalMaterialsData;
use App\Domains\InclusiveRadar\Domain\Enums\ResourceStatus;
use Tests\TestCase;

final class ListAccessibleEducationalMaterialsDataTest extends TestCase
{
    public function test_it_maps_snake_case_filters_and_preserves_false_values(): void
    {
        $filters = ListAccessibleEducationalMaterialsData::from([
            'name' => 'Braille',
            'status' => ResourceStatus::UNAVAILABLE->value,
            'is_digital' => false,
            'is_active' => false,
            'available' => false,
            'per_page' => 20,
        ]);

        self::assertSame('Braille', $filters->name);
        self::assertSame(ResourceStatus::UNAVAILABLE, $filters->status);
        self::assertFalse($filters->isDigital);
        self::assertFalse($filters->isActive);
        self::assertFalse($filters->available);
        self::assertSame(20, $filters->perPage);
    }

    public function test_it_applies_empty_filter_defaults(): void
    {
        $filters = ListAccessibleEducationalMaterialsData::from([]);

        self::assertNull($filters->name);
        self::assertNull($filters->status);
        self::assertNull($filters->isDigital);
        self::assertNull($filters->isActive);
        self::assertNull($filters->available);
        self::assertSame(10, $filters->perPage);
    }
}
