<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Feature\InstitutionalEvents;

use App\Domains\InclusiveRadar\Application\Actions\InstitutionalEvents\UpdateInstitutionalEventAction;
use App\Domains\InclusiveRadar\Application\Data\InstitutionalEvents\UpdateInstitutionalEventData;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidInstitutionalEvent;
use App\Domains\InclusiveRadar\Domain\Models\InstitutionalEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class UpdateInstitutionalEventActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_updates_an_institutional_event(): void
    {
        $event = InstitutionalEvent::factory()->create([
            'title' => 'Evento Antigo',
        ]);

        $updated = app(UpdateInstitutionalEventAction::class)->execute(
            $event,
            new UpdateInstitutionalEventData(
                title: 'Evento Atualizado',
                startDate: '2026-08-01',
                endDate: '2026-08-01',
                startTime: '09:00',
                endTime: '11:00',
                location: 'Biblioteca',
                description: 'Descrição atualizada.',
                organizer: 'Equipe Atualizada',
                audience: 'Público geral',
                isActive: true,
            ),
        );

        self::assertSame('Evento Atualizado', $updated->title);
        $this->assertDatabaseHas('institutional_events', [
            'id' => $event->id,
            'title' => 'Evento Atualizado',
            'location' => 'Biblioteca',
        ]);
    }

    public function test_it_rejects_invalid_same_day_time_range(): void
    {
        $event = InstitutionalEvent::factory()->create();

        $this->expectException(InvalidInstitutionalEvent::class);

        app(UpdateInstitutionalEventAction::class)->execute(
            $event,
            new UpdateInstitutionalEventData(
                title: 'Evento Inválido',
                startDate: '2026-08-01',
                endDate: '2026-08-01',
                startTime: '11:00',
                endTime: '09:00',
                location: 'Biblioteca',
                isActive: true,
            ),
        );
    }
}
