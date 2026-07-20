<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Unit\Application\Data\Waitlists;

use App\Domains\InclusiveRadar\Application\Data\Waitlists\ListWaitlistsData;
use App\Domains\InclusiveRadar\Domain\Enums\WaitlistStatus;
use Tests\TestCase;

final class ListWaitlistsDataTest extends TestCase
{
    public function test_it_maps_filters_and_status_to_domain_types(): void
    {
        $data = ListWaitlistsData::from([
            'item' => 'Linha Braille',
            'student' => 'Ana',
            'professional' => 'Marley',
            'status' => WaitlistStatus::WAITING->value,
            'start_date' => '2026-07-01',
            'end_date' => '2026-07-20',
            'per_page' => 25,
        ]);

        self::assertSame('Linha Braille', $data->item);
        self::assertSame('Ana', $data->student);
        self::assertSame('Marley', $data->professional);
        self::assertSame(WaitlistStatus::WAITING, $data->status);
        self::assertSame('2026-07-01', $data->startDate);
        self::assertSame('2026-07-20', $data->endDate);
        self::assertSame(25, $data->perPage);
    }

    public function test_it_applies_default_filters(): void
    {
        $data = ListWaitlistsData::from([]);

        self::assertNull($data->item);
        self::assertNull($data->student);
        self::assertNull($data->professional);
        self::assertNull($data->status);
        self::assertNull($data->startDate);
        self::assertNull($data->endDate);
        self::assertSame(10, $data->perPage);
    }
}
