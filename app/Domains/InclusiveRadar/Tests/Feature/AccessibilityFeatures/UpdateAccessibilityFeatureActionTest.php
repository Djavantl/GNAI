<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Feature\AccessibilityFeatures;

use App\Domains\InclusiveRadar\Application\Actions\AccessibilityFeatures\UpdateAccessibilityFeatureAction;
use App\Domains\InclusiveRadar\Application\Data\AccessibilityFeatures\UpdateAccessibilityFeatureData;
use App\Domains\InclusiveRadar\Domain\Models\AccessibilityFeature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class UpdateAccessibilityFeatureActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_updates_an_accessibility_feature(): void
    {
        $feature = AccessibilityFeature::factory()->create([
            'name' => 'Audiodescrição',
            'description' => 'Antiga.',
            'is_active' => true,
        ]);

        $updated = app(UpdateAccessibilityFeatureAction::class)->execute(
            feature: $feature,
            data: new UpdateAccessibilityFeatureData(
                name: 'Libras',
                description: 'Nova.',
                isActive: false,
            ),
        );

        self::assertSame('Libras', $updated->name);
        $this->assertDatabaseHas('accessibility_features', [
            'id' => $feature->id,
            'name' => 'Libras',
            'description' => 'Nova.',
            'is_active' => false,
        ]);
    }
}
