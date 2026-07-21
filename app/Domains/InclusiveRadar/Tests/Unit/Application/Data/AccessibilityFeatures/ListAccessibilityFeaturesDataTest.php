<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Unit\Application\Data\AccessibilityFeatures;

use App\Domains\InclusiveRadar\Application\Data\AccessibilityFeatures\ListAccessibilityFeaturesData;
use Tests\TestCase;

final class ListAccessibilityFeaturesDataTest extends TestCase
{
    public function test_it_maps_filters_and_preserves_false_values(): void
    {
        $data = ListAccessibilityFeaturesData::from([
            'name' => 'Braille',
            'is_active' => false,
            'per_page' => 25,
        ]);

        self::assertSame('Braille', $data->name);
        self::assertFalse($data->isActive);
        self::assertSame(25, $data->perPage);
    }

    public function test_it_applies_default_filters(): void
    {
        $data = ListAccessibilityFeaturesData::from([]);

        self::assertNull($data->name);
        self::assertNull($data->isActive);
        self::assertSame(10, $data->perPage);
    }
}
