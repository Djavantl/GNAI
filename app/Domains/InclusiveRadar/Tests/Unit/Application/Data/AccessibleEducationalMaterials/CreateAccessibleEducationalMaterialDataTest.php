<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Unit\Application\Data\AccessibleEducationalMaterials;

use App\Domains\InclusiveRadar\Application\Data\AccessibleEducationalMaterials\CreateAccessibleEducationalMaterialData;
use App\Domains\InclusiveRadar\Domain\Enums\ConservationState;
use App\Domains\InclusiveRadar\Domain\Enums\InspectionType;
use App\Domains\InclusiveRadar\Domain\Enums\ResourceStatus;
use Illuminate\Validation\Rules\Enum;
use Tests\TestCase;

final class CreateAccessibleEducationalMaterialDataTest extends TestCase
{
    public function test_it_builds_validation_rules_using_snake_case(): void
    {
        $rules = CreateAccessibleEducationalMaterialData::getValidationRules([
            'name' => 'Livro em Braille',
            'is_digital' => false,
            'is_loanable' => true,
            'quantity' => 2,
            'asset_code' => null,
            'conservation_state' => ConservationState::GOOD->value,
            'deficiencies' => [1],
            'accessibility_features' => [1],
            'inspection' => [
                'date' => '2026-07-18',
                'type' => InspectionType::INITIAL->value,
            ],
            'status' => ResourceStatus::AVAILABLE->value,
            'is_active' => true,
        ]);

        self::assertArrayHasKey('is_digital', $rules);
        self::assertArrayHasKey('asset_code', $rules);
        self::assertArrayHasKey('conservation_state', $rules);
        self::assertArrayHasKey('accessibility_features', $rules);
        self::assertArrayHasKey('accessibility_features.*', $rules);
        self::assertArrayHasKey('inspection.date', $rules);
        self::assertArrayHasKey('inspection.type', $rules);
        self::assertArrayHasKey('inspection.evidences.*', $rules);
        self::assertArrayHasKey('is_active', $rules);
        self::assertArrayNotHasKey('isDigital', $rules);
        self::assertContainsOnlyInstancesOf(Enum::class, array_filter(
            $rules['conservation_state'],
            static fn (mixed $rule): bool => $rule instanceof Enum,
        ));
    }

    public function test_it_maps_snake_case_input_and_domain_enums(): void
    {
        $data = CreateAccessibleEducationalMaterialData::from([
            'name' => 'Livro em Braille',
            'is_digital' => false,
            'is_loanable' => true,
            'quantity' => 2,
            'asset_code' => 'MPA-1001',
            'conservation_state' => ConservationState::GOOD->value,
            'deficiencies' => [1, 2],
            'accessibility_features' => [3, 4],
            'inspection' => [
                'date' => '2026-07-18',
                'type' => InspectionType::INITIAL->value,
                'description' => 'Vistoria inicial',
            ],
            'notes' => null,
            'status' => ResourceStatus::AVAILABLE->value,
            'is_active' => true,
        ]);

        self::assertSame('Livro em Braille', $data->name);
        self::assertFalse($data->isDigital);
        self::assertTrue($data->isLoanable);
        self::assertSame(2, $data->quantity);
        self::assertSame('MPA-1001', $data->assetCode);
        self::assertSame(ConservationState::GOOD, $data->conservationState);
        self::assertSame([1, 2], $data->deficiencies);
        self::assertSame([3, 4], $data->accessibilityFeatures);
        self::assertSame('2026-07-18', $data->inspection->date);
        self::assertSame(InspectionType::INITIAL, $data->inspection->type);
        self::assertSame(ResourceStatus::AVAILABLE, $data->status);
        self::assertTrue($data->isActive);
    }

    public function test_it_applies_creation_defaults(): void
    {
        $data = CreateAccessibleEducationalMaterialData::from([
            'name' => 'Mapa tátil',
            'is_digital' => false,
            'is_loanable' => true,
            'quantity' => 2,
            'asset_code' => null,
            'conservation_state' => ConservationState::GOOD->value,
            'deficiencies' => [1],
            'inspection' => [
                'date' => '2026-07-18',
            ],
        ]);

        self::assertSame([], $data->accessibilityFeatures);
        self::assertSame(InspectionType::INITIAL, $data->inspection->type);
        self::assertSame(ResourceStatus::AVAILABLE, $data->status);
        self::assertTrue($data->isActive);
        self::assertSame([], $data->inspection->evidences);
        self::assertNull($data->inspection->description);
    }
}
