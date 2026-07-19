<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Unit\Domain\Models;

use App\Domains\InclusiveRadar\Domain\DTOs\Inspections\CreateInspectionDTO;
use App\Domains\InclusiveRadar\Domain\Enums\ConservationState;
use App\Domains\InclusiveRadar\Domain\Enums\InspectionType;
use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;
use App\Domains\InclusiveRadar\Domain\Models\Inspection;
use App\Domains\InclusiveRadar\Domain\Models\InspectionImage;
use Tests\TestCase;

final class InspectionTest extends TestCase
{
    public function test_it_casts_the_inspection_state_and_type(): void
    {
        $inspection = Inspection::register(new CreateInspectionDTO(
            date: '2026-07-18',
            type: InspectionType::INITIAL,
            registeredBy: 1,
            state: ConservationState::GOOD->value,
        ));

        self::assertSame(ConservationState::GOOD, $inspection->state);
        self::assertSame(InspectionType::INITIAL, $inspection->type);
        self::assertSame('2026-07-18', $inspection->inspection_date->toDateString());
    }

    public function test_it_uses_the_new_models_in_its_relationships(): void
    {
        $technology = new AssistiveTechnology;
        $inspection = new Inspection;
        $image = new InspectionImage;

        self::assertInstanceOf(
            Inspection::class,
            $technology->inspections()->getRelated(),
        );
        self::assertInstanceOf(
            InspectionImage::class,
            $inspection->images()->getRelated(),
        );
        self::assertInstanceOf(
            Inspection::class,
            $image->inspection()->getRelated(),
        );
    }
}
