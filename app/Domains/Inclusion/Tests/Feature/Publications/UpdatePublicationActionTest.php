<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Feature\Publications;

use App\Domains\Inclusion\Application\Actions\Publications\UpdatePublicationAction;
use App\Domains\Inclusion\Application\Data\Publications\UpdatePublicationData;
use App\Domains\Inclusion\Domain\Exceptions\InvalidPublication;
use App\Domains\Inclusion\Domain\Models\Professional;
use App\Domains\Inclusion\Domain\Models\Publication;
use App\Domains\Inclusion\Domain\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class UpdatePublicationActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_updates_the_publication_and_replaces_its_speakers_and_tags(): void
    {
        $publication = Publication::factory()->create(['title' => 'Evento antigo']);
        $oldTag = Tag::factory()->create();
        $newTag = Tag::factory()->create();
        $publication->tags()->attach($oldTag);
        $publication->speakers()->create(['professional_id' => Professional::factory()->create()->id, 'position' => 0]);

        app(UpdatePublicationAction::class)->execute($publication, new UpdatePublicationData(
            title: 'Evento atualizado',
            startDate: '2026-08-01',
            endDate: '2026-08-01',
            startTime: '09:00',
            endTime: '11:00',
            location: 'Biblioteca',
            isActive: true,
            speakers: [['name' => 'Convidado Externo', 'participation' => 'Debatedor']],
            tagIds: [$newTag->id],
        ));

        $this->assertDatabaseHas('publications', [
            'id' => $publication->id,
            'title' => 'Evento atualizado',
            'location' => 'Biblioteca',
        ]);
        $this->assertDatabaseMissing('publication_tag', ['publication_id' => $publication->id, 'tag_id' => $oldTag->id]);
        $this->assertDatabaseHas('publication_tag', ['publication_id' => $publication->id, 'tag_id' => $newTag->id]);
        $this->assertDatabaseCount('publication_speakers', 1);
        $this->assertDatabaseHas('publication_speakers', [
            'publication_id' => $publication->id,
            'professional_id' => null,
            'name' => 'Convidado Externo',
            'participation' => 'Debatedor',
        ]);
    }

    public function test_it_replaces_the_cover_and_removes_gallery_photos_deleting_their_files(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('publications/old-cover.png', 'cover');
        Storage::disk('public')->put('publications/kept.png', 'kept');
        Storage::disk('public')->put('publications/removed.png', 'removed');
        $publication = Publication::factory()->create([
            'cover_path' => 'publications/old-cover.png',
            'cover_alt' => 'Capa antiga',
        ]);
        $kept = $publication->media()->create(['path' => 'publications/kept.png', 'alt_text' => 'Antiga', 'position' => 1]);
        $removed = $publication->media()->create(['path' => 'publications/removed.png', 'alt_text' => 'Remover', 'position' => 2]);

        app(UpdatePublicationAction::class)->execute($publication, new UpdatePublicationData(
            title: $publication->title,
            hasSchedule: false,
            cover: UploadedFile::fake()->create('new-cover.png', 10, 'image/png'),
            coverAlt: 'Capa nova',
            galleryExisting: [
                ['id' => $kept->id, 'alt' => 'Descrição revisada'],
                ['id' => $removed->id, 'remove' => '1'],
            ],
        ));

        Storage::disk('public')->assertMissing('publications/old-cover.png');
        Storage::disk('public')->assertMissing('publications/removed.png');
        Storage::disk('public')->assertExists('publications/kept.png');
        Storage::disk('public')->assertExists($publication->refresh()->cover_path);
        $this->assertDatabaseHas('publication_media', ['id' => $kept->id, 'alt_text' => 'Descrição revisada']);
        $this->assertDatabaseMissing('publication_media', ['id' => $removed->id]);
    }

    public function test_it_requires_alternative_text_for_kept_gallery_photos(): void
    {
        $publication = Publication::factory()->create();
        $photo = $publication->media()->create(['path' => 'publications/photo.png', 'alt_text' => 'Foto', 'position' => 1]);

        $this->expectException(InvalidPublication::class);
        $this->expectExceptionMessage('Todas as fotos da galeria precisam de texto alternativo.');

        app(UpdatePublicationAction::class)->execute($publication, new UpdatePublicationData(
            title: $publication->title,
            hasSchedule: false,
            galleryExisting: [['id' => $photo->id, 'alt' => ' ']],
        ));
    }
}
