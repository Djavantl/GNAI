<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Feature\AccessibleEducationalMaterials;

use App\Domains\InclusiveRadar\Domain\DTOs\AccessibleEducationalMaterials\CreateAccessibleEducationalMaterialDTO;
use App\Domains\InclusiveRadar\Domain\Enums\ConservationState;
use App\Domains\InclusiveRadar\Domain\Enums\InspectionType;
use App\Domains\InclusiveRadar\Domain\Enums\ResourceStatus;
use App\Domains\InclusiveRadar\Domain\Models\AccessibleEducationalMaterial;
use App\Domains\InclusiveRadar\Domain\ValueObjects\AssetCode;
use App\Domains\InclusiveRadar\UI\Controllers\AccessibleEducationalMaterialController;
use App\Domains\InclusiveRadar\Domain\Models\AccessibilityFeature;
use App\Models\SpecializedEducationalSupport\Deficiency;
use App\Domains\Auth\Domain\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

final class CreateAccessibleEducationalMaterialTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/_tests/inclusive-radar/accessible-educational-materials';

    protected function setUp(): void
    {
        parent::setUp();

        Route::post(
            self::ENDPOINT,
            [AccessibleEducationalMaterialController::class, 'store'],
        )->middleware('web');
    }

    public function test_it_creates_a_physical_accessible_educational_material(): void
    {
        $user = User::factory()->create();
        $deficiencies = Deficiency::factory()->count(2)->create();
        $features = AccessibilityFeature::factory()->count(2)->create();

        $response = $this->actingAs($user)->post(self::ENDPOINT, [
            'name' => 'Livro em Braille',
            'is_digital' => false,
            'is_loanable' => true,
            'quantity' => 3,
            'asset_code' => 'MPA-1001',
            'conservation_state' => ConservationState::GOOD->value,
            'deficiencies' => $deficiencies->modelKeys(),
            'accessibility_features' => $features->modelKeys(),
            'inspection' => [
                'date' => now()->toDateString(),
                'type' => InspectionType::INITIAL->value,
                'description' => 'Cadastro inicial',
                'images' => [],
            ],
            'status' => ResourceStatus::AVAILABLE->value,
            'is_active' => true,
        ]);

        $response->assertRedirect(
            route('inclusive-radar.accessible-educational-materials.index'),
        );
        $response->assertSessionHas('success', 'Material criado com sucesso!');

        $this->assertDatabaseHas('accessible_educational_materials', [
            'name' => 'Livro em Braille',
            'asset_code' => 'MPA-1001',
            'quantity' => 3,
            'quantity_available' => 3,
            'is_digital' => false,
            'is_loanable' => true,
        ]);

        $materialId = (int) DB::table('accessible_educational_materials')
            ->where('asset_code', 'MPA-1001')
            ->value('id');

        foreach ($deficiencies as $deficiency) {
            $this->assertDatabaseHas('accessible_educational_material_deficiency', [
                'accessible_educational_material_id' => $materialId,
                'deficiency_id' => $deficiency->id,
            ]);
        }

        foreach ($features as $feature) {
            $this->assertDatabaseHas('accessible_educational_material_accessibility', [
                'accessible_educational_material_id' => $materialId,
                'accessibility_feature_id' => $feature->id,
            ]);
        }

        $this->assertDatabaseHas('inspections', [
            'inspectable_id' => $materialId,
            'inspectable_type' => 'accessible_educational_material',
            'state' => ConservationState::GOOD->value,
            'status' => null,
            'type' => InspectionType::INITIAL->value,
            'description' => 'Cadastro inicial',
            'user_id' => $user->id,
        ]);
    }

    public function test_it_creates_a_digital_material_without_stock(): void
    {
        $user = User::factory()->create();
        $deficiency = Deficiency::factory()->create();

        $response = $this->actingAs($user)->post(self::ENDPOINT, [
            'name' => 'Apostila digital acessível',
            'is_digital' => true,
            'is_loanable' => true,
            'quantity' => null,
            'asset_code' => null,
            'conservation_state' => ConservationState::NOT_APPLICABLE->value,
            'deficiencies' => [$deficiency->id],
            'accessibility_features' => [],
            'inspection' => [
                'date' => now()->toDateString(),
                'type' => InspectionType::INITIAL->value,
            ],
            'status' => ResourceStatus::AVAILABLE->value,
            'is_active' => true,
        ]);

        $response->assertRedirect(
            route('inclusive-radar.accessible-educational-materials.index'),
        );

        $this->assertDatabaseHas('accessible_educational_materials', [
            'name' => 'Apostila digital acessível',
            'is_digital' => true,
            'is_loanable' => true,
            'quantity' => null,
            'quantity_available' => null,
        ]);
    }

    public function test_it_rejects_creation_without_target_audience(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->from('/accessible-educational-materials/create')
            ->post(self::ENDPOINT, [
                'name' => 'Mapa tátil',
                'is_digital' => false,
                'is_loanable' => true,
                'quantity' => 1,
                'asset_code' => null,
                'conservation_state' => ConservationState::GOOD->value,
                'deficiencies' => [],
                'accessibility_features' => [],
                'inspection' => [
                    'date' => now()->toDateString(),
                    'type' => InspectionType::INITIAL->value,
                ],
                'status' => ResourceStatus::AVAILABLE->value,
                'is_active' => true,
            ]);

        $response->assertRedirect('/accessible-educational-materials/create');
        $response->assertSessionHasErrors('deficiencies');
        $this->assertDatabaseMissing('accessible_educational_materials', [
            'name' => 'Mapa tátil',
        ]);
    }

    public function test_it_rejects_a_normalized_asset_code_already_in_use(): void
    {
        $existingMaterial = AccessibleEducationalMaterial::register(new CreateAccessibleEducationalMaterialDTO(
            name: 'Livro existente',
            isDigital: false,
            isLoanable: true,
            quantity: 1,
            assetCode: AssetCode::from('MPA-2001'),
            conservationState: ConservationState::GOOD,
        ));
        $existingMaterial->save();

        $user = User::factory()->create();
        $deficiency = Deficiency::factory()->create();

        $response = $this->actingAs($user)
            ->from('/accessible-educational-materials/create')
            ->post(self::ENDPOINT, [
                'name' => 'Livro duplicado',
                'is_digital' => false,
                'is_loanable' => true,
                'quantity' => 1,
                'asset_code' => ' MPA-2001 ',
                'conservation_state' => ConservationState::GOOD->value,
                'deficiencies' => [$deficiency->id],
                'accessibility_features' => [],
                'inspection' => [
                    'date' => now()->toDateString(),
                    'type' => InspectionType::INITIAL->value,
                ],
                'status' => ResourceStatus::AVAILABLE->value,
                'is_active' => true,
            ]);

        $response->assertRedirect('/accessible-educational-materials/create');
        $response->assertSessionHasErrors([
            'asset_code' => 'O código patrimonial já está em uso.',
        ]);
        $this->assertDatabaseCount('accessible_educational_materials', 1);
    }
}
