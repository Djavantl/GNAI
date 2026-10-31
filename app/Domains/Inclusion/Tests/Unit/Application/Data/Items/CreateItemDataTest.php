<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Unit\Application\Data\Items;

use App\Domains\Inclusion\Application\Data\Items\CreateItemData;
use App\Domains\Inclusion\Domain\Enums\ConservationState;
use App\Domains\Inclusion\Domain\Enums\InspectionType;
use App\Domains\Inclusion\Domain\Enums\ItemType;
use App\Domains\Inclusion\Domain\Enums\ResourceStatus;
use Tests\TestCase;

final class CreateItemDataTest extends TestCase
{
    public function test_it_maps_snake_case_input_and_nested_inspection(): void
    {
        $data = CreateItemData::from([
            'item_type' => ItemType::AccessibleEducationalMaterial->value,
            'name' => 'Livro em Braille',
            'is_digital' => false,
            'is_loanable' => true,
            'quantity' => 2,
            'asset_code' => 'MPA-1001',
            'conservation_state' => ConservationState::GOOD->value,
            'deficiencies' => [1, 2],
            'accessibility_features' => [3],
            'inspection' => [
                'date' => '2026-07-18',
                'description' => 'Vistoria inicial',
            ],
        ]);

        self::assertSame(ItemType::AccessibleEducationalMaterial, $data->itemType);
        self::assertFalse($data->isDigital);
        self::assertTrue($data->isLoanable);
        self::assertSame('MPA-1001', $data->assetCode);
        self::assertSame(ConservationState::GOOD, $data->conservationState);
        self::assertSame([1, 2], $data->deficiencies);
        self::assertSame([3], $data->accessibilityFeatures);
        self::assertSame('Vistoria inicial', $data->inspection->description);
    }

    public function test_it_applies_creation_defaults(): void
    {
        $data = CreateItemData::from([
            'item_type' => ItemType::AssistiveTechnology->value,
            'name' => 'Leitor de tela',
            'is_digital' => true,
            'is_loanable' => false,
            'quantity' => null,
            'asset_code' => null,
            'conservation_state' => ConservationState::NOT_APPLICABLE->value,
            'deficiencies' => [1],
            'inspection' => ['date' => '2026-07-18'],
        ]);

        self::assertSame(ResourceStatus::AVAILABLE, $data->status);
        self::assertTrue($data->isActive);
        self::assertSame([], $data->accessibilityFeatures);
        self::assertSame(InspectionType::INITIAL, $data->inspection->type);
    }

    public function test_it_requires_quantity_only_for_physical_items(): void
    {
        $physical = CreateItemData::getValidationRules(['is_digital' => false]);
        $digital = CreateItemData::getValidationRules(['is_digital' => '1']);

        self::assertContains('required', $physical['quantity']);
        self::assertContains('min:1', $physical['quantity']);
        self::assertContains('nullable', $digital['quantity']);
        self::assertContains('min:0', $digital['quantity']);
    }
}
