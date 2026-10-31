<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Feature\Publications;

use App\Domains\Inclusion\Application\Actions\Publications\UpdatePublicationAction;
use App\Domains\Inclusion\Application\Data\Publications\UpdatePublicationData;
use App\Domains\Inclusion\Domain\Exceptions\InvalidPublication;
use App\Domains\Inclusion\Domain\Models\Publication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class UpdatePublicationActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_updates_an_publication(): void
    {
        $publication = Publication::factory()->create([
            'title' => 'Evento Antigo',
        ]);

        $updated = app(UpdatePublicationAction::class)->execute(
            $publication,
            new UpdatePublicationData(
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
        $this->assertDatabaseHas('publications', [
            'id' => $publication->id,
            'title' => 'Evento Atualizado',
            'location' => 'Biblioteca',
        ]);
    }

    public function test_it_rejects_invalid_same_day_time_range(): void
    {
        $publication = Publication::factory()->create();

        $this->expectException(InvalidPublication::class);

        app(UpdatePublicationAction::class)->execute(
            $publication,
            new UpdatePublicationData(
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
