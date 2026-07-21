<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Feature\Institutions;

use App\Domains\InclusiveRadar\Application\Actions\Institutions\DeleteInstitutionAction;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidInstitution;
use App\Domains\InclusiveRadar\Domain\Models\Institution;
use App\Models\InclusiveRadar\Barrier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class DeleteInstitutionActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_deletes_an_institution_without_active_barriers(): void
    {
        $institution = Institution::factory()->create();

        app(DeleteInstitutionAction::class)->execute($institution);

        $this->assertSoftDeleted('institutions', [
            'id' => $institution->id,
        ]);
    }

    public function test_it_rejects_deleting_an_institution_with_active_barriers(): void
    {
        $institution = Institution::factory()->create();
        Barrier::factory()->create([
            'institution_id' => $institution->id,
        ]);

        $this->expectException(InvalidInstitution::class);
        $this->expectExceptionMessage('Não é possível excluir esta instituição pois ela possui barreiras ativas.');

        app(DeleteInstitutionAction::class)->execute($institution);
    }
}
