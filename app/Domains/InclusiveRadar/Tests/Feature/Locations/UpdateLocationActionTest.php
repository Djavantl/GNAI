<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Feature\Locations;

use App\Domains\InclusiveRadar\Application\Actions\Locations\UpdateLocationAction;
use App\Domains\InclusiveRadar\Application\Data\Locations\UpdateLocationData;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidLocation;
use App\Domains\InclusiveRadar\Domain\Models\Barrier;
use App\Domains\InclusiveRadar\Domain\Models\Institution;
use App\Domains\InclusiveRadar\Domain\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class UpdateLocationActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_updates_a_location(): void
    {
        $institution = Institution::factory()->create();
        $location = Location::factory()->create([
            'institution_id' => $institution->id,
            'name' => 'Nome Original',
            'is_active' => true,
        ]);

        $updated = app(UpdateLocationAction::class)->execute(
            $location,
            new UpdateLocationData(
                institutionId: $institution->id,
                name: 'Novo Nome',
                latitude: -14.22,
                longitude: -42.78,
                type: 'Laboratório',
                description: null,
                googlePlaceId: null,
                isActive: true,
            ),
        );

        self::assertSame('Novo Nome', $updated->name);
        $this->assertDatabaseHas('locations', [
            'id' => $location->id,
            'name' => 'Novo Nome',
            'type' => 'Laboratório',
            'is_active' => true,
        ]);
    }

    public function test_it_prevents_deactivation_when_location_has_unresolved_barriers(): void
    {
        $institution = Institution::factory()->create();
        $location = Location::factory()->create([
            'institution_id' => $institution->id,
            'is_active' => true,
        ]);

        Barrier::factory()->create([
            'institution_id' => $institution->id,
            'location_id' => $location->id,
            'resolved_at' => null,
        ]);

        $this->expectException(InvalidLocation::class);

        app(UpdateLocationAction::class)->execute(
            $location,
            new UpdateLocationData(
                institutionId: $institution->id,
                name: $location->name,
                latitude: $location->latitude,
                longitude: $location->longitude,
                type: $location->type,
                description: $location->description,
                googlePlaceId: $location->google_place_id,
                isActive: false,
            ),
        );
    }
}
