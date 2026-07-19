<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Unit\Application\Data\AssistiveTechnologies;

use App\Domains\InclusiveRadar\Application\Data\AssistiveTechnologies\UpdateAssistiveTechnologyData;
use App\Domains\InclusiveRadar\Domain\Enums\ConservationState;
use App\Domains\InclusiveRadar\Domain\Enums\InspectionType;
use App\Domains\InclusiveRadar\Domain\Enums\ResourceStatus;
use Tests\TestCase;

final class UpdateAssistiveTechnologyDataTest extends TestCase
{
    public function test_it_builds_nested_validation_rules_using_snake_case(): void
    {
        $rules = UpdateAssistiveTechnologyData::getValidationRules($this->payload());

        self::assertArrayHasKey('is_digital', $rules);
        self::assertArrayHasKey('is_loanable', $rules);
        self::assertArrayHasKey('asset_code', $rules);
        self::assertArrayHasKey('conservation_state', $rules);
        self::assertArrayHasKey('inspection.date', $rules);
        self::assertArrayHasKey('inspection.type', $rules);
        self::assertArrayHasKey('inspection.images.*', $rules);
        self::assertArrayHasKey('is_active', $rules);
        self::assertArrayNotHasKey('isDigital', $rules);
    }

    public function test_it_maps_update_input_to_domain_types(): void
    {
        $data = UpdateAssistiveTechnologyData::from($this->payload());

        self::assertFalse($data->isDigital);
        self::assertTrue($data->isLoanable);
        self::assertSame(5, $data->quantity);
        self::assertSame('TA-1001', $data->assetCode);
        self::assertSame(ConservationState::REGULAR, $data->conservationState);
        self::assertSame(ResourceStatus::AVAILABLE, $data->status);
        self::assertSame(InspectionType::PERIODIC, $data->inspection->type);
        self::assertSame('Reavaliação periódica', $data->inspection->description);
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(): array
    {
        return [
            'name' => 'Linha Braille',
            'is_digital' => false,
            'is_loanable' => true,
            'quantity' => 5,
            'asset_code' => 'TA-1001',
            'conservation_state' => ConservationState::REGULAR->value,
            'status' => ResourceStatus::AVAILABLE->value,
            'is_active' => true,
            'deficiencies' => [1],
            'inspection' => [
                'date' => '2026-07-18',
                'type' => InspectionType::PERIODIC->value,
                'description' => 'Reavaliação periódica',
            ],
        ];
    }
}
