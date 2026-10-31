<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Unit\Application\Data\Items;

use App\Domains\Inclusion\Application\Data\Items\UpdateItemData;
use Tests\TestCase;

final class UpdateItemDataTest extends TestCase
{
    public function test_it_requires_quantity_only_for_physical_items(): void
    {
        $physical = UpdateItemData::getValidationRules(['is_digital' => '0']);
        $digital = UpdateItemData::getValidationRules(['is_digital' => true]);

        self::assertContains('required', $physical['quantity']);
        self::assertContains('min:1', $physical['quantity']);
        self::assertContains('nullable', $digital['quantity']);
        self::assertContains('min:0', $digital['quantity']);
    }

    public function test_it_requires_an_inspection_type_on_update(): void
    {
        $rules = UpdateItemData::getValidationRules(['is_digital' => false]);

        self::assertContains('required', $rules['inspection.type']);
    }
}
