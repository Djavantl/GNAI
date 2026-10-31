<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Feature\Publications;

use App\Domains\Inclusion\Application\Actions\Publications\CreatePublicationAction;
use App\Domains\Inclusion\Application\Data\Publications\CreatePublicationData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CreatePublicationActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_an_publication(): void
    {
        $publication = app(CreatePublicationAction::class)->execute(
            new CreatePublicationData(
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

        self::assertSame('Semana da Educação Inclusiva', $publication->title);
        $this->assertDatabaseHas('publications', [
            'id' => $publication->id,
            'title' => 'Semana da Educação Inclusiva',
            'location' => 'Auditório Central',
            'is_active' => true,
        ]);
    }
}
