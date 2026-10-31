<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Feature\Tags;

use App\Domains\Inclusion\Application\Actions\Tags\DeleteTagAction;
use App\Domains\Inclusion\Domain\Models\Publication;
use App\Domains\Inclusion\Domain\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class DeleteTagActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_detaches_the_tag_from_publications_without_deleting_them(): void
    {
        $tag = Tag::factory()->create();
        $publication = Publication::factory()->create();
        $publication->tags()->attach($tag);

        app(DeleteTagAction::class)->execute($tag);

        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
        $this->assertDatabaseMissing('publication_tag', ['tag_id' => $tag->id]);
        $this->assertDatabaseHas('publications', ['id' => $publication->id]);
    }
}
