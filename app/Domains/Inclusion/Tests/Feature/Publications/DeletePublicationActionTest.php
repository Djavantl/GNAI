<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Feature\Publications;

use App\Domains\Inclusion\Application\Actions\Publications\DeletePublicationAction;
use App\Domains\Inclusion\Domain\Models\Publication;
use App\Domains\Inclusion\Domain\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class DeletePublicationActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_deletes_the_publication_with_its_relations_and_files(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('publications/cover.png', 'cover');
        Storage::disk('public')->put('publications/photo.png', 'photo');
        $tag = Tag::factory()->create();
        $publication = Publication::factory()->create([
            'cover_path' => 'publications/cover.png',
            'cover_alt' => 'Capa',
        ]);
        $publication->media()->create(['path' => 'publications/photo.png', 'alt_text' => 'Foto', 'position' => 1]);
        $publication->speakers()->create(['name' => 'Convidada', 'position' => 0]);
        $publication->tags()->attach($tag);

        app(DeletePublicationAction::class)->execute($publication);

        $this->assertDatabaseMissing('publications', ['id' => $publication->id]);
        $this->assertDatabaseMissing('publication_tag', ['publication_id' => $publication->id]);
        $this->assertDatabaseMissing('publication_speakers', ['publication_id' => $publication->id]);
        $this->assertDatabaseHas('tags', ['id' => $tag->id]);
        Storage::disk('public')->assertMissing('publications/cover.png');
        Storage::disk('public')->assertMissing('publications/photo.png');
    }
}
