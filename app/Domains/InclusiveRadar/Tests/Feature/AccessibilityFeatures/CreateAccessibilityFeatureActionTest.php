<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Feature\AccessibilityFeatures;

use App\Domains\InclusiveRadar\Application\Actions\AccessibilityFeatures\CreateAccessibilityFeatureAction;
use App\Domains\InclusiveRadar\Application\Data\AccessibilityFeatures\CreateAccessibilityFeatureData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CreateAccessibilityFeatureActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_an_accessibility_feature(): void
    {
        $feature = app(CreateAccessibilityFeatureAction::class)->execute(
            new CreateAccessibilityFeatureData(
                name: 'Audiodescrição',
                description: 'Recurso de apoio.',
                isActive: true,
            ),
        );

        self::assertSame('Audiodescrição', $feature->name);
        $this->assertDatabaseHas('accessibility_features', [
            'id' => $feature->id,
            'name' => 'Audiodescrição',
            'description' => 'Recurso de apoio.',
            'is_active' => true,
        ]);
    }
}
