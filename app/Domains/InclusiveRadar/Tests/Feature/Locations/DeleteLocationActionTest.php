<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Feature\Locations;

use App\Domains\InclusiveRadar\Application\Actions\Locations\DeleteLocationAction;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidLocation;
use App\Domains\InclusiveRadar\Domain\Models\Institution;
use App\Domains\InclusiveRadar\Domain\Models\Location;
use App\Models\InclusiveRadar\Barrier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class DeleteLocationActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_deletes_a_location(): void
    {
        $location = Location::factory()->create();

        app(DeleteLocationAction::class)->execute($location);

        $this->assertSoftDeleted('locations', [
            'id' => $location->id,
        ]);
    }

    public function test_it_prevents_deletion_when_location_has_unresolved_barriers(): void
    {
        $institution = Institution::factory()->create();
        $location = Location::factory()->create([
            'institution_id' => $institution->id,
        ]);

        Barrier::factory()->create([
            'institution_id' => $institution->id,
            'location_id' => $location->id,
            'resolved_at' => null,
        ]);

        $this->expectException(InvalidLocation::class);

        app(DeleteLocationAction::class)->execute($location);
    }
}
