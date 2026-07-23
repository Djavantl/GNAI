<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Unit\Application\Data\InstitutionalEvents;

use App\Domains\InclusiveRadar\Application\Data\InstitutionalEvents\CreateInstitutionalEventData;
use Tests\TestCase;

final class CreateInstitutionalEventDataTest extends TestCase
{
    public function test_it_maps_snake_case_input(): void
    {
        $data = CreateInstitutionalEventData::from([
            'title' => 'Semana Inclusiva',
            'start_date' => '2026-08-01',
            'end_date' => '2026-08-01',
            'start_time' => '08:00',
            'end_time' => '10:00',
            'location' => 'Auditório',
            'is_active' => true,
        ]);

        self::assertSame('Semana Inclusiva', $data->title);
        self::assertSame('2026-08-01', $data->startDate);
        self::assertSame('2026-08-01', $data->endDate);
        self::assertSame('08:00', $data->startTime);
        self::assertSame('10:00', $data->endTime);
        self::assertSame('Auditório', $data->location);
        self::assertTrue($data->isActive);
    }

    public function test_it_applies_checkbox_defaults(): void
    {
        $data = CreateInstitutionalEventData::from([
            'title' => 'Semana Inclusiva',
            'start_date' => '2026-08-01',
            'end_date' => '2026-08-01',
            'start_time' => '08:00',
            'end_time' => '10:00',
            'location' => 'Auditório',
        ]);

        self::assertFalse($data->isActive);
    }
}
