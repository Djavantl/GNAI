<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Feature\Institutions;

use App\Domains\InclusiveRadar\Application\Actions\Institutions\UpdateInstitutionAction;
use App\Domains\InclusiveRadar\Application\Data\Institutions\UpdateInstitutionData;
use App\Domains\InclusiveRadar\Domain\Models\Institution;
use App\Domains\InclusiveRadar\Domain\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class UpdateInstitutionActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_updates_an_institution(): void
    {
        $institution = Institution::factory()->create([
            'name' => 'Antiga',
            'is_active' => true,
        ]);

        $updated = app(UpdateInstitutionAction::class)->execute(
            institution: $institution,
            data: new UpdateInstitutionData(
                name: 'Nova',
                city: 'Guanambi',
                state: 'Bahia',
                latitude: -14.22,
                longitude: -42.77,
                isActive: true,
            ),
        );

        self::assertSame('Nova', $updated->name);
        $this->assertDatabaseHas('institutions', [
            'id' => $institution->id,
            'name' => 'Nova',
        ]);
    }

    public function test_it_deactivates_locations_when_institution_is_deactivated(): void
    {
        $institution = Institution::factory()->create([
            'is_active' => true,
        ]);
        $locations = Location::factory()->count(2)->create([
            'institution_id' => $institution->id,
            'is_active' => true,
        ]);

        app(UpdateInstitutionAction::class)->execute(
            institution: $institution,
            data: new UpdateInstitutionData(
                name: $institution->name,
                city: $institution->city,
                state: $institution->state,
                latitude: (float) $institution->latitude,
                longitude: (float) $institution->longitude,
                defaultZoom: $institution->default_zoom,
                isActive: false,
            ),
        );

        foreach ($locations as $location) {
            self::assertFalse($location->fresh()->is_active);
        }
    }
}
