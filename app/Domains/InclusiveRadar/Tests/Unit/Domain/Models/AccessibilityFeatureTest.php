<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Unit\Domain\Models;

use App\Domains\InclusiveRadar\Domain\DTOs\AccessibilityFeatures\CreateAccessibilityFeatureDTO;
use App\Domains\InclusiveRadar\Domain\DTOs\AccessibilityFeatures\UpdateAccessibilityFeatureDTO;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidAccessibilityFeature;
use App\Domains\InclusiveRadar\Domain\Models\AccessibilityFeature;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Tests\TestCase;

final class AccessibilityFeatureTest extends TestCase
{
    public function test_it_registers_an_accessibility_feature(): void
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

    public function test_it_revises_an_accessibility_feature(): void
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

    public function test_it_rejects_empty_name(): void
    {
        $this->expectException(InvalidAccessibilityFeature::class);
        $this->expectExceptionMessage('O nome do recurso é obrigatório.');

        AccessibilityFeature::register(new CreateAccessibilityFeatureDTO(
            name: ' ',
        ));
    }

    public function test_it_has_materials_relationship(): void
    {
        $feature = new AccessibilityFeature();

        $relation = $feature->materials();

        self::assertInstanceOf(BelongsToMany::class, $relation);
        self::assertSame('accessible_educational_material_accessibility', $relation->getTable());
    }
}
