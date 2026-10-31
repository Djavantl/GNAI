<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Unit\Application\Data\AccessibilityFeatures;

use App\Domains\Inclusion\Application\Data\AccessibilityFeatures\UpdateAccessibilityFeatureData;
use Tests\TestCase;

final class UpdateAccessibilityFeatureDataTest extends TestCase
{
    public function test_it_treats_an_unchecked_active_checkbox_as_inactive(): void
    {
        $data = UpdateAccessibilityFeatureData::from([
            'name' => 'Libras',
        ]);

        self::assertFalse($data->isActive);
    }
}
