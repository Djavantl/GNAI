<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Unit\Application\Data\AccessibilityFeatures;

use App\Domains\InclusiveRadar\Application\Data\AccessibilityFeatures\CreateAccessibilityFeatureData;
use Tests\TestCase;

final class CreateAccessibilityFeatureDataTest extends TestCase
{
    public function test_it_maps_snake_case_input(): void
    {
        $data = CreateAccessibilityFeatureData::from([
            'name' => 'Audiodescrição',
            'description' => 'Recurso de apoio.',
            'is_active' => true,
        ]);

        self::assertSame('Audiodescrição', $data->name);
        self::assertSame('Recurso de apoio.', $data->description);
        self::assertTrue($data->isActive);
    }

    public function test_it_applies_checkbox_defaults(): void
    {
        $data = CreateAccessibilityFeatureData::from([
            'name' => 'Libras',
        ]);

        self::assertFalse($data->isActive);
    }
}
