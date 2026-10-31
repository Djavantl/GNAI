<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Unit\Domain\Models;

use App\Domains\Inclusion\Domain\DTOs\InstitutionalEvents\CreateInstitutionalEventDTO;
use App\Domains\Inclusion\Domain\Exceptions\InvalidInstitutionalEvent;
use App\Domains\Inclusion\Domain\Models\InstitutionalEvent;
use Tests\TestCase;

final class InstitutionalEventTest extends TestCase
{
    public function test_it_schedules_an_event(): void
    {
        $event = InstitutionalEvent::schedule(new CreateInstitutionalEventDTO(
            title: ' Semana Inclusiva ',
            startDate: '2026-08-01',
            endDate: '2026-08-01',
            startTime: '08:00',
            endTime: '10:00',
            location: ' Auditório ',
            isActive: true,
        ));

        self::assertSame('Semana Inclusiva', $event->title);
        self::assertSame('Auditório', $event->location);
        self::assertTrue($event->is_active);
    }

    public function test_it_rejects_end_date_before_start_date(): void
    {
        $this->expectException(InvalidInstitutionalEvent::class);

        InstitutionalEvent::schedule(new CreateInstitutionalEventDTO(
            title: 'Evento Inválido',
            startDate: '2026-08-02',
            endDate: '2026-08-01',
            startTime: '08:00',
            endTime: '10:00',
            location: 'Auditório',
        ));
    }
}
