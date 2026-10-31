<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Unit\Application\Data\Waitlists;

use App\Domains\Inclusion\Application\Data\Waitlists\CreateWaitlistData;
use App\Domains\Inclusion\Domain\Enums\ItemType;
use Tests\TestCase;

final class CreateWaitlistDataTest extends TestCase
{
    public function test_it_maps_snake_case_input(): void
    {
        $data = CreateWaitlistData::from([
            'waitlistable_id' => 10,
            'item_type' => ItemType::AccessibleEducationalMaterial->value,
            'professional_id' => 30,
            'observation' => 'Aguardar disponibilidade.',
        ]);

        self::assertSame(10, $data->waitlistableId);
        self::assertSame(ItemType::AccessibleEducationalMaterial, $data->itemType);
        self::assertNull($data->studentId);
        self::assertSame(30, $data->professionalId);
        self::assertSame('Aguardar disponibilidade.', $data->observation);
    }
}
