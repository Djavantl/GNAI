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

    public function test_it_opens_creation_with_reusable_data_and_empty_unique_and_inspection_fields(): void
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

        $response->assertOk();
        $response->assertViewIs('pages.inclusive-radar.accessible-educational-materials.create');
        $response->assertSee('Clonar Material Pedagógico Acessível');
        $response->assertSee('value="Livro tátil de geografia"', false);
        $response->assertSee('Material adaptado em alto relevo');
        $response->assertSee('value="3"', false);
        $response->assertSee('value="unavailable"', false);

        foreach ($deficiencies as $deficiency) {
            self::assertMatchesRegularExpression(
                '/id="def_'.$deficiency->id.'"[^>]*checked/',
                $response->getContent(),
            );
        }

        foreach ($features as $feature) {
            self::assertMatchesRegularExpression(
                '/id="feat_'.$feature->id.'"[^>]*checked/',
                $response->getContent(),
            );
        }

        self::assertDoesNotMatchRegularExpression(
            '/name="asset_code"[^>]*value="MPA-ORIGINAL-01"/',
            $response->getContent(),
        );
        self::assertMatchesRegularExpression('/name="asset_code"[^>]*value=""/', $response->getContent());
        self::assertMatchesRegularExpression('/name="inspection\[date\]"[^>]*value=""/', $response->getContent());
        $response->assertDontSee('Cadastro inicial do material');
        $response->assertDontSee('foto-original-material.jpg');
    }
}
