<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Feature\Publications;

use App\Domains\Inclusion\Application\Actions\Publications\DeletePublicationAction;
use App\Domains\Inclusion\Domain\Models\Publication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class DeletePublicationActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_deletes_an_publication(): void
    {
        $publication = Publication::factory()->create();

        app(DeletePublicationAction::class)->execute($publication);

        $this->assertDatabaseMissing('publications', [
            'id' => $publication->id,
        ]);
    }
}
