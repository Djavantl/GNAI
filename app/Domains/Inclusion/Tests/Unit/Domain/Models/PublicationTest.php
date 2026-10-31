<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Unit\Domain\Models;

use App\Domains\Inclusion\Domain\DTOs\Publications\CreatePublicationDTO;
use App\Domains\Inclusion\Domain\Exceptions\InvalidPublication;
use App\Domains\Inclusion\Domain\Models\Publication;
use Tests\TestCase;

final class PublicationTest extends TestCase
{
    public function test_it_rejects_an_end_date_before_the_start_date(): void
    {
        $this->expectException(InvalidPublication::class);
        $this->expectExceptionMessage('A data de término não pode ser anterior à data de início.');

        Publication::schedule(new CreatePublicationDTO(
            title: 'Evento inválido',
            startDate: '2026-08-02',
            endDate: '2026-08-01',
            startTime: '08:00',
            endTime: '10:00',
            location: 'Auditório',
        ));
    }

    public function test_it_rejects_an_end_time_not_after_the_start_time_on_the_same_day(): void
    {
        $this->expectException(InvalidPublication::class);
        $this->expectExceptionMessage('O horário de término deve ser maior que o horário de início para o mesmo dia.');

        Publication::schedule(new CreatePublicationDTO(
            title: 'Evento inválido',
            startDate: '2026-08-01',
            endDate: '2026-08-01',
            startTime: '11:00',
            endTime: '11:00',
            location: 'Auditório',
        ));
    }

    public function test_it_accepts_an_earlier_end_time_on_a_later_day(): void
    {
        $publication = Publication::schedule(new CreatePublicationDTO(
            title: 'Evento de dois dias',
            startDate: '2026-08-01',
            endDate: '2026-08-02',
            startTime: '14:00',
            endTime: '10:00',
            location: 'Auditório',
        ));

        self::assertSame('2026-08-02', $publication->end_date->toDateString());
        self::assertSame('10:00', $publication->end_time->format('H:i'));
    }
}
