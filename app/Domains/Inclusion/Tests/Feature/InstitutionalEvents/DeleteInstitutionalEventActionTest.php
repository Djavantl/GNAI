<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Feature\InstitutionalEvents;

use App\Domains\Inclusion\Application\Actions\InstitutionalEvents\DeleteInstitutionalEventAction;
use App\Domains\Inclusion\Domain\Models\InstitutionalEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class DeleteInstitutionalEventActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_deletes_an_institutional_event(): void
    {
        $event = InstitutionalEvent::factory()->create();

        app(DeleteInstitutionalEventAction::class)->execute($event);

        $this->assertDatabaseMissing('institutional_events', [
            'id' => $event->id,
        ]);
    }
}
