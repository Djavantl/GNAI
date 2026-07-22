<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Feature\Locations;

use App\Domains\InclusiveRadar\Application\Actions\Locations\CreateLocationAction;
use App\Domains\InclusiveRadar\Application\Data\Locations\CreateLocationData;
use App\Domains\InclusiveRadar\Domain\Models\Institution;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CreateLocationActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_location(): void
    {
        $institution = Institution::factory()->create();

        $location = app(CreateLocationAction::class)->execute(
            new CreateLocationData(
                institutionId: $institution->id,
                name: 'Biblioteca Central',
                latitude: -14.223,
                longitude: -42.781,
                type: 'Biblioteca',
                description: 'Prédio principal.',
                googlePlaceId: 'place-123',
                isActive: true,
            ),
        );

        self::assertSame('Biblioteca Central', $location->name);
        $this->assertDatabaseHas('locations', [
            'id' => $location->id,
            'institution_id' => $institution->id,
            'name' => 'Biblioteca Central',
            'type' => 'Biblioteca',
            'description' => 'Prédio principal.',
            'google_place_id' => 'place-123',
            'is_active' => true,
        ]);
    }
}
