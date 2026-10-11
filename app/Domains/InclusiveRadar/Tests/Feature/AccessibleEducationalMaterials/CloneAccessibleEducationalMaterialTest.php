<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Feature\AccessibleEducationalMaterials;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\InclusiveRadar\Domain\Enums\ConservationState;
use App\Domains\InclusiveRadar\Domain\Enums\ResourceStatus;
use App\Domains\InclusiveRadar\Domain\Models\AccessibilityFeature;
use App\Domains\InclusiveRadar\Domain\Models\AccessibleEducationalMaterial;
use App\Domains\InclusiveRadar\Domain\Models\Inspection;
use App\Domains\InclusiveRadar\Domain\Models\InspectionEvidence;
use App\Domains\InclusiveRadar\UI\Controllers\AccessibleEducationalMaterialController;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Deficiency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

final class CloneAccessibleEducationalMaterialTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/_tests/inclusive-radar/accessible-educational-materials/{material}/clone';

    protected function setUp(): void
    {
        parent::setUp();

        Route::get(self::ENDPOINT, [AccessibleEducationalMaterialController::class, 'clone'])
            ->middleware('web');
    }

    public function test_it_sends_reusable_source_data_and_form_options_to_react(): void
    {
        $user = User::factory()->create();
        $deficiencies = Deficiency::factory()->count(2)->create();
        $features = AccessibilityFeature::factory()->count(2)->create();
        $material = AccessibleEducationalMaterial::factory()->physical()->create([
            'name' => 'Livro tátil de geografia',
            'notes' => 'Material adaptado em alto relevo',
            'asset_code' => 'MPA-ORIGINAL-01',
            'quantity' => 3,
            'quantity_available' => 1,
            'conservation_state' => ConservationState::REGULAR,
            'status' => ResourceStatus::UNAVAILABLE,
            'is_loanable' => true,
            'is_active' => false,
        ]);
        $material->deficiencies()->attach($deficiencies->modelKeys());
        $material->accessibilityFeatures()->attach($features->modelKeys());
        $inspection = Inspection::factory()->forAccessibleEducationalMaterial($material)->create([
            'description' => 'Cadastro inicial do material',
        ]);
        InspectionEvidence::factory()->for($inspection)->create([
            'original_name' => 'foto-original-material.jpg',
        ]);

        $response = $this->actingAs($user)->get(
            str_replace('{material}', (string) $material->id, self::ENDPOINT),
        );

        $response->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('InclusiveRadar/AccessibleEducationalMaterials/Create')
            ->where('cloneSource.id', $material->id)
            ->where('cloneSource.name', 'Livro tátil de geografia')
            ->where('cloneSource.notes', 'Material adaptado em alto relevo')
            ->where('cloneSource.quantity', 3)
            ->where('cloneSource.status', 'unavailable')
            ->has('cloneSource.deficiencies', 2)
            ->has('cloneSource.accessibility_features', $features->count())
            ->missing('cloneSource.inspections')
            ->missing('cloneSource.inspection_evidences')
            ->has('resourceStatuses')
        );
    }
}
