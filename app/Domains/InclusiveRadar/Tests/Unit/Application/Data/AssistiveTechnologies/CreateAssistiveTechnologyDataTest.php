<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Unit\Application\Data\AssistiveTechnologies;

use App\Domains\InclusiveRadar\Application\Data\AssistiveTechnologies\CreateAssistiveTechnologyData;
use App\Domains\InclusiveRadar\Domain\Enums\ConservationState;
use App\Domains\InclusiveRadar\Domain\Enums\InspectionType;
use App\Domains\InclusiveRadar\Domain\Enums\ResourceStatus;
use Illuminate\Validation\Rules\Enum;
use Tests\TestCase;

final class CreateAssistiveTechnologyDataTest extends TestCase
{
    public function test_it_builds_validation_rules_using_snake_case(): void
    {
        $rules = CreateAssistiveTechnologyData::getValidationRules([
            'name' => 'Leitor de tela',
            'is_digital' => true,
            'is_loanable' => true,
            'quantity' => null,
            'asset_code' => null,
            'conservation_state' => ConservationState::NOT_APPLICABLE->value,
            'deficiencies' => [1],
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
        self::assertArrayHasKey('inspection.date', $rules);
        self::assertArrayHasKey('inspection.type', $rules);
        self::assertArrayHasKey('inspection.evidences.*', $rules);
        self::assertContains('max:20480', $rules['inspection.evidences.*']);
        self::assertNotContains('max:2048', $rules['inspection.evidences.*']);
        self::assertArrayHasKey('is_active', $rules);
        self::assertArrayNotHasKey('isDigital', $rules);
        self::assertArrayNotHasKey('assetCode', $rules);
        self::assertContainsOnlyInstancesOf(Enum::class, array_filter(
            $rules['conservation_state'],
            static fn (mixed $rule): bool => $rule instanceof Enum,
        ));
        self::assertNotEmpty(array_filter(
            $rules['status'],
            static fn (mixed $rule): bool => $rule instanceof Enum,
        ));
    }

    public function test_it_maps_snake_case_input_and_domain_enums(): void
    {
        $data = CreateAssistiveTechnologyData::from([
            'name' => 'Leitor de tela',
            'is_digital' => true,
            'is_loanable' => true,
            'quantity' => null,
            'asset_code' => 'TA-1001',
            'conservation_state' => ConservationState::NOT_APPLICABLE->value,
            'deficiencies' => [1, 2],
            'inspection' => [
                'date' => '2026-07-18',
                'type' => InspectionType::INITIAL->value,
                'description' => 'Vistoria inicial',
            ],
            'notes' => null,
            'status' => ResourceStatus::AVAILABLE->value,
            'is_active' => true,
        ]);

        self::assertSame('Leitor de tela', $data->name);
        self::assertTrue($data->isDigital);
        self::assertTrue($data->isLoanable);
        self::assertNull($data->quantity);
        self::assertSame('TA-1001', $data->assetCode);
        self::assertSame(ConservationState::NOT_APPLICABLE, $data->conservationState);
        self::assertSame([1, 2], $data->deficiencies);
        self::assertSame('2026-07-18', $data->inspection->date);
        self::assertSame(InspectionType::INITIAL, $data->inspection->type);
        self::assertSame('Vistoria inicial', $data->inspection->description);
        self::assertSame(ResourceStatus::AVAILABLE, $data->status);
        self::assertTrue($data->isActive);
    }

    public function test_it_applies_creation_defaults(): void
    {
        $data = CreateAssistiveTechnologyData::from([
            'name' => 'Teclado adaptado',
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

        self::assertSame(InspectionType::INITIAL, $data->inspection->type);
        self::assertSame(ResourceStatus::AVAILABLE, $data->status);
        self::assertTrue($data->isActive);
        self::assertSame([], $data->inspection->evidences);
        self::assertNull($data->inspection->description);
        self::assertNull($data->notes);
    }
}
