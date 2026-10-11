<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Feature\AssistiveTechnologies;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\InclusiveRadar\Domain\Enums\ConservationState;
use App\Domains\InclusiveRadar\Domain\Enums\ResourceStatus;
use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;
use App\Domains\InclusiveRadar\Domain\Models\Inspection;
use App\Domains\InclusiveRadar\Domain\Models\InspectionEvidence;
use App\Domains\InclusiveRadar\UI\Controllers\AssistiveTechnologyController;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Deficiency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

final class CloneAssistiveTechnologyTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/_tests/inclusive-radar/assistive-technologies/{assistiveTechnology}/clone';

    protected function setUp(): void
    {
        parent::setUp();

        Route::get(self::ENDPOINT, [AssistiveTechnologyController::class, 'clone'])
            ->middleware('web');
    }

    public function test_it_sends_reusable_source_data_and_form_options_to_react(): void
    {
        $user = User::factory()->create();
        $deficiencies = Deficiency::factory()->count(2)->create();
        $technology = AssistiveTechnology::factory()->physical()->create([
            'name' => 'Cadeira de rodas motorizada',
            'notes' => 'Modelo para uso institucional',
            'asset_code' => 'TA-ORIGINAL-01',
            'quantity' => 4,
            'quantity_available' => 2,
            'conservation_state' => ConservationState::GOOD,
            'status' => ResourceStatus::UNAVAILABLE,
            'is_loanable' => true,
            'is_active' => false,
        ]);
        $technology->deficiencies()->attach($deficiencies->modelKeys());
        $inspection = Inspection::factory()->forAssistiveTechnology($technology)->create([
            'description' => 'Cadastro inicial da tecnologia',
        ]);
        InspectionEvidence::factory()->for($inspection)->create([
            'original_name' => 'foto-original-tecnologia.jpg',
        ]);

        $response = $this->actingAs($user)->get(
            str_replace('{assistiveTechnology}', (string) $technology->id, self::ENDPOINT),
        );

        $response->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('InclusiveRadar/AssistiveTechnologies/Create')
            ->where('cloneSource.id', $technology->id)
            ->where('cloneSource.name', 'Cadeira de rodas motorizada')
            ->where('cloneSource.notes', 'Modelo para uso institucional')
            ->where('cloneSource.quantity', 4)
            ->where('cloneSource.status', 'unavailable')
            ->has('cloneSource.deficiencies', 2)
            ->missing('cloneSource.inspections')
            ->missing('cloneSource.inspection_evidences')
            ->has('resourceStatuses')
        );
    }
}
