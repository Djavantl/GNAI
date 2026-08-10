<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Unit\Domain\Models;

use App\Domains\InclusiveRadar\Domain\DTOs\Locations\CreateLocationDTO;
use App\Domains\InclusiveRadar\Domain\DTOs\Locations\UpdateLocationDTO;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidLocation;
use App\Domains\InclusiveRadar\Domain\Models\Institution;
use App\Domains\InclusiveRadar\Domain\Models\Location;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tests\TestCase;

final class LocationTest extends TestCase
{
    public function test_it_registers_a_location(): void
    {
        $location = Location::register(new CreateLocationDTO(
            institutionId: 10,
            name: ' Biblioteca ',
            latitude: -14.22,
            longitude: -42.77,
            type: ' Prédio ',
            description: ' Local de estudo. ',
            googlePlaceId: ' place-123 ',
            isActive: true,
        ));

        self::assertSame(10, $location->institution_id);
        self::assertSame('Biblioteca', $location->name);
        self::assertSame('Prédio', $location->type);
        self::assertSame('Local de estudo.', $location->description);
        self::assertSame(-14.22, $location->latitude);
        self::assertSame(-42.77, $location->longitude);
        self::assertSame('place-123', $location->google_place_id);
        self::assertTrue($location->is_active);
    }

    public function test_it_revises_a_location(): void
    {
        $location = new Location([
            'institution_id' => 10,
            'name' => 'Biblioteca',
            'type' => 'Prédio',
            'description' => 'Antiga.',
            'latitude' => -14.22,
            'longitude' => -42.77,
            'google_place_id' => 'place-123',
            'is_active' => true,
        ]);

        $location->revise(new UpdateLocationDTO(
            institutionId: 20,
            name: 'Sala Multifuncional',
            latitude: -12.97,
            longitude: -38.50,
            type: '',
            description: '',
            googlePlaceId: '',
            isActive: false,
        ));

        self::assertSame(20, $location->institution_id);
        self::assertSame('Sala Multifuncional', $location->name);
        self::assertNull($location->type);
        self::assertNull($location->description);
        self::assertSame(-12.97, $location->latitude);
        self::assertSame(-38.50, $location->longitude);
        self::assertNull($location->google_place_id);
        self::assertFalse($location->is_active);
    }

    public function test_it_trims_name(): void
    {
        $location = Location::register(new CreateLocationDTO(
            institutionId: 10,
            name: ' Biblioteca ',
            latitude: -14.22,
            longitude: -42.77,
        ));

        self::assertSame('Biblioteca', $location->name);
    }

    public function test_it_rejects_invalid_latitude(): void
    {
        $this->expectException(InvalidLocation::class);
        $this->expectExceptionMessage('A latitude deve estar entre -90 e 90.');

        Location::register(new CreateLocationDTO(
            institutionId: 10,
            name: 'Biblioteca',
            latitude: -100,
            longitude: -42.77,
        ));
    }

    public function test_it_rejects_invalid_longitude(): void
    {
        $this->expectException(InvalidLocation::class);
        $this->expectExceptionMessage('A longitude deve estar entre -180 e 180.');

        Location::register(new CreateLocationDTO(
            institutionId: 10,
            name: 'Biblioteca',
            latitude: -14.22,
            longitude: -200,
        ));
    }

    public function test_it_has_institution_relationship(): void
    {
        $location = new Location();

        $relation = $location->institution();

        self::assertInstanceOf(BelongsTo::class, $relation);
        self::assertSame(Institution::class, $relation->getRelated()::class);
    }

    public function test_it_has_barriers_relationship(): void
    {
        $location = new Location();

        $relation = $location->barriers();

        self::assertInstanceOf(HasMany::class, $relation);
    }
}
