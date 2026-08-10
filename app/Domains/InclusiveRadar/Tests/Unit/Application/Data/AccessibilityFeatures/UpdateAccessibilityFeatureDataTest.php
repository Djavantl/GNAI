<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Unit\Application\Data\AccessibilityFeatures;

use App\Domains\InclusiveRadar\Application\Data\AccessibilityFeatures\UpdateAccessibilityFeatureData;
use Tests\TestCase;

final class UpdateAccessibilityFeatureDataTest extends TestCase
{
    public function test_it_maps_snake_case_input(): void
    {
        $data = UpdateAccessibilityFeatureData::from([
            'name' => 'Libras',
            'description' => 'Recurso atualizado.',
            'is_active' => false,
        ]);

        self::assertSame('Libras', $data->name);
        self::assertSame('Recurso atualizado.', $data->description);
        self::assertFalse($data->isActive);
    }
}
