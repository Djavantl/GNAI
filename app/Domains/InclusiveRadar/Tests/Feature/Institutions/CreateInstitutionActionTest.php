<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Feature\Institutions;

use App\Domains\InclusiveRadar\Application\Actions\Institutions\CreateInstitutionAction;
use App\Domains\InclusiveRadar\Application\Data\Institutions\CreateInstitutionData;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidInstitution;
use App\Domains\InclusiveRadar\Domain\Models\Institution;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CreateInstitutionActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_an_institution(): void
    {
        $institution = app(CreateInstitutionAction::class)->execute(
            new CreateInstitutionData(
                name: 'IFBA Campus Guanambi',
                city: 'Guanambi',
                state: 'Bahia',
                latitude: -14.22,
                longitude: -42.77,
                shortName: 'IFBA-GBI',
                defaultZoom: 16,
                isActive: true,
            ),
        );

        self::assertSame('IFBA Campus Guanambi', $institution->name);
        $this->assertDatabaseHas('institutions', [
            'id' => $institution->id,
            'name' => 'IFBA Campus Guanambi',
            'short_name' => 'IFBA-GBI',
            'is_active' => true,
        ]);
    }

    public function test_it_rejects_creating_a_duplicate_institution(): void
    {
        Institution::factory()->create([
            'name' => 'Primeira Instituição',
            'city' => 'Guanambi',
            'state' => 'Bahia',
            'latitude' => -14.22,
            'longitude' => -42.77,
        ]);

        $this->expectException(InvalidInstitution::class);
        $this->expectExceptionMessage('Já existe uma instituição cadastrada com esses dados.');

        app(CreateInstitutionAction::class)->execute(
            new CreateInstitutionData(
                name: 'Primeira Instituição',
                city: 'Guanambi',
                state: 'Bahia',
                latitude: -14.22,
                longitude: -42.77,
            ),
        );
    }
}
