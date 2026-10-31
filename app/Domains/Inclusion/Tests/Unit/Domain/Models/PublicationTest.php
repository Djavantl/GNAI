<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Unit\Domain\Models;

use App\Domains\Inclusion\Domain\DTOs\Publications\CreatePublicationDTO;
use App\Domains\Inclusion\Domain\Exceptions\InvalidPublication;
use App\Domains\Inclusion\Domain\Models\Publication;
use Tests\TestCase;

final class PublicationTest extends TestCase
{
    public function test_it_schedules_an_event(): void
    {
        $publication = Publication::schedule(new CreatePublicationDTO(
            title: ' Semana Inclusiva ',
            startDate: '2026-08-01',
            endDate: '2026-08-01',
            startTime: '08:00',
            endTime: '10:00',
            location: ' Auditório ',
            isActive: true,
        ));

        self::assertSame('Semana Inclusiva', $publication->title);
        self::assertSame('Auditório', $publication->location);
        self::assertTrue($publication->is_active);
    }

    public function test_it_rejects_end_date_before_start_date(): void
    {
        $this->expectException(InvalidPublication::class);

        Publication::schedule(new CreatePublicationDTO(
            title: 'Evento Inválido',
            startDate: '2026-08-02',
            endDate: '2026-08-01',
            startTime: '08:00',
            endTime: '10:00',
            location: 'Auditório',
        ));
    }
}
