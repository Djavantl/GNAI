<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Unit\Domain\Models;

use App\Domains\Inclusion\Domain\DTOs\AccessibilityFeatures\CreateAccessibilityFeatureDTO;
use App\Domains\Inclusion\Domain\DTOs\AccessibilityFeatures\UpdateAccessibilityFeatureDTO;
use App\Domains\Inclusion\Domain\Models\AccessibilityFeature;
use Tests\TestCase;

final class AccessibilityFeatureTest extends TestCase
{
    public function test_it_trims_name_and_description_on_registration(): void
    {
        $feature = AccessibilityFeature::register(new CreateAccessibilityFeatureDTO(
            name: ' Audiodescrição ',
            description: ' Recurso de apoio. ',
            isActive: true,
        ));

        self::assertSame('Audiodescrição', $feature->name);
        self::assertSame('Recurso de apoio.', $feature->description);
        self::assertTrue($feature->is_active);
    }

    public function test_it_clears_a_blank_description_on_revision(): void
    {
        $feature = new AccessibilityFeature([
            'name' => 'Audiodescrição',
            'description' => 'Antiga.',
            'is_active' => true,
        ]);

        $feature->revise(new UpdateAccessibilityFeatureDTO(
            name: 'Libras',
            description: '',
            isActive: false,
        ));

        self::assertSame('Libras', $feature->name);
        self::assertNull($feature->description);
        self::assertFalse($feature->is_active);
    }
}
