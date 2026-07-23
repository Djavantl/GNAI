<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Feature\InstitutionalEvents;

use App\Domains\InclusiveRadar\Application\Actions\InstitutionalEvents\CreateInstitutionalEventAction;
use App\Domains\InclusiveRadar\Application\Data\InstitutionalEvents\CreateInstitutionalEventData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CreateInstitutionalEventActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_an_institutional_event(): void
    {
        $event = app(CreateInstitutionalEventAction::class)->execute(
            new CreateInstitutionalEventData(
                title: 'Semana da Educação Inclusiva',
                startDate: '2026-08-01',
                endDate: '2026-08-01',
                startTime: '08:00',
                endTime: '10:00',
                location: 'Auditório Central',
                description: 'Evento institucional.',
                organizer: 'Equipe Radar',
                audience: 'Comunidade',
                isActive: true,
            ),
        );

        self::assertSame('Semana da Educação Inclusiva', $event->title);
        $this->assertDatabaseHas('institutional_events', [
            'id' => $event->id,
            'title' => 'Semana da Educação Inclusiva',
            'location' => 'Auditório Central',
            'is_active' => true,
        ]);
    }
}
