<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Unit\Application\Data\Inspections;

use App\Domains\InclusiveRadar\Application\Data\Inspections\CreateInspectionData;
use App\Domains\InclusiveRadar\Domain\Enums\InspectionType;
use Illuminate\Validation\Rules\Enum;
use Tests\TestCase;

final class CreateInspectionDataTest extends TestCase
{
    public function test_it_maps_an_inspection_and_applies_defaults(): void
    {
        $data = CreateInspectionData::from([
            'date' => '2026-07-18',
        ]);

        self::assertSame('2026-07-18', $data->date);
        self::assertSame(InspectionType::INITIAL, $data->type);
        self::assertNull($data->description);
        self::assertSame([], $data->images);
    }

    public function test_it_infers_the_inspection_type_enum_rule(): void
    {
        $rules = CreateInspectionData::getValidationRules([
            'date' => '2026-07-18',
            'type' => InspectionType::PERIODIC->value,
        ]);

        self::assertNotEmpty(array_filter(
            $rules['type'],
            static fn (mixed $rule): bool => $rule instanceof Enum,
        ));
        self::assertContains('max:10', $rules['images']);
        self::assertContains('max:5120', $rules['images.*']);
    }
}
