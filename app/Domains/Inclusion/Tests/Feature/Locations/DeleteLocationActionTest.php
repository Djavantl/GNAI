<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Feature\Locations;

use App\Domains\Inclusion\Application\Actions\Locations\DeleteLocationAction;
use App\Domains\Inclusion\Domain\Exceptions\InvalidLocation;
use App\Domains\Inclusion\Domain\Models\Barrier;
use App\Domains\Inclusion\Domain\Models\Institution;
use App\Domains\Inclusion\Domain\Models\Location;
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
