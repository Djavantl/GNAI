<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Feature\Publications;

use App\Domains\Inclusion\Application\Actions\Publications\CreatePublicationAction;
use App\Domains\Inclusion\Application\Data\Publications\CreatePublicationData;
use App\Domains\Inclusion\Domain\Exceptions\InvalidPublication;
use App\Domains\Inclusion\Domain\Models\Professional;
use App\Domains\Inclusion\Domain\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class CreatePublicationActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_an_event_with_tags_and_registered_and_guest_speakers(): void
    {
        $professional = Professional::factory()->create();
        [$accessibility, $libras] = Tag::factory()->count(2)->create();

        $publication = app(CreatePublicationAction::class)->execute(new CreatePublicationData(
            title: '  Semana da Educação Inclusiva  ',
            startDate: '2026-08-01',
            endDate: '2026-08-02',
            startTime: '08:00',
            endTime: '10:00',
            location: 'Auditório Central',
            isActive: true,
            speakers: [
                ['id' => $professional->id, 'participation' => ' Palestrante '],
                ['name' => '  Maria Convidada ', 'participation' => 'Mediadora'],
                ['id' => $professional->id],
                ['name' => '   '],
            ],
            tagIds: [$accessibility->id, (string) $libras->id, $accessibility->id],
        ));

        $this->assertDatabaseHas('publications', [
            'id' => $publication->id,
            'title' => 'Semana da Educação Inclusiva',
            'slug' => 'semana-da-educacao-inclusiva',
            'has_schedule' => true,
            'location' => 'Auditório Central',
        ]);
        self::assertNotNull($publication->published_at);
        self::assertSame(
            [[$professional->id, null, 'Palestrante'], [null, 'Maria Convidada', 'Mediadora']],
            $publication->speakers()->get()->map(fn ($speaker) => [
                $speaker->professional_id,
                $speaker->name,
                $speaker->participation,
            ])->all(),
        );
        self::assertEqualsCanonicalizing(
            [$accessibility->id, $libras->id],
            $publication->tags()->pluck('tags.id')->all(),
        );
    }

    public function test_it_ignores_schedule_fields_when_the_publication_has_no_schedule(): void
    {
        $publication = app(CreatePublicationAction::class)->execute(new CreatePublicationData(
            title: 'Comunicado institucional',
            startDate: '2026-08-01',
            endDate: '2026-08-01',
            startTime: '08:00',
            endTime: '10:00',
            hasSchedule: false,
            type: 'announcement',
        ));

        $this->assertDatabaseHas('publications', [
            'id' => $publication->id,
            'type' => 'announcement',
            'has_schedule' => false,
            'start_date' => null,
            'end_time' => null,
        ]);
    }

    public function test_it_requires_the_full_period_for_scheduled_publications(): void
    {
        $this->expectException(InvalidPublication::class);
        $this->expectExceptionMessage('Informe as datas e os horários de início e término do evento.');

        app(CreatePublicationAction::class)->execute(new CreatePublicationData(
            title: 'Evento sem horário',
            startDate: '2026-08-01',
            endDate: '2026-08-01',
        ));
    }

    public function test_it_stores_the_cover_and_gallery_with_their_alternative_texts(): void
    {
        Storage::fake('public');

        $publication = app(CreatePublicationAction::class)->execute(new CreatePublicationData(
            title: 'Registro da formação',
            hasSchedule: false,
            cover: UploadedFile::fake()->create('cover.png', 10, 'image/png'),
            coverAlt: 'Participantes no auditório',
            gallery: [
                ['file' => UploadedFile::fake()->create('one.png', 10, 'image/png'), 'alt' => ' Mesa de abertura '],
                ['file' => UploadedFile::fake()->create('two.png', 10, 'image/png'), 'alt' => 'Oficina de Libras'],
            ],
        ));

        Storage::disk('public')->assertExists($publication->cover_path);
        self::assertSame(
            ['Mesa de abertura', 'Oficina de Libras'],
            $publication->media()->pluck('alt_text')->all(),
        );
        foreach ($publication->media()->pluck('path') as $path) {
            Storage::disk('public')->assertExists($path);
        }
    }

    public function test_it_rejects_a_cover_without_alternative_text_and_discards_the_uploaded_files(): void
    {
        Storage::fake('public');

        try {
            app(CreatePublicationAction::class)->execute(new CreatePublicationData(
                title: 'Capa sem descrição',
                hasSchedule: false,
                cover: UploadedFile::fake()->create('cover.png', 10, 'image/png'),
                gallery: [['file' => UploadedFile::fake()->create('one.png', 10, 'image/png'), 'alt' => 'Foto']],
            ));
            self::fail('A capa sem texto alternativo deveria ser rejeitada.');
        } catch (InvalidPublication $exception) {
            self::assertSame('Descreva a imagem de capa no texto alternativo.', $exception->getMessage());
        }

        self::assertSame([], Storage::disk('public')->allFiles());
        $this->assertDatabaseCount('publications', 0);
    }
}
