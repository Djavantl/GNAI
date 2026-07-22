<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Feature\AccessibleEducationalMaterials;

use App\Domains\InclusiveRadar\Domain\DTOs\AccessibleEducationalMaterials\CreateAccessibleEducationalMaterialDTO;
use App\Domains\InclusiveRadar\Domain\Enums\ConservationState;
use App\Domains\InclusiveRadar\Domain\Enums\InspectionType;
use App\Domains\InclusiveRadar\Domain\Enums\LoanStatus;
use App\Domains\InclusiveRadar\Domain\Enums\ResourceStatus;
use App\Domains\InclusiveRadar\Domain\Models\AccessibleEducationalMaterial;
use App\Domains\InclusiveRadar\Domain\Models\Loan;
use App\Domains\InclusiveRadar\Domain\ValueObjects\AssetCode;
use App\Domains\InclusiveRadar\UI\Controllers\AccessibleEducationalMaterialController;
use App\Domains\InclusiveRadar\Domain\Models\AccessibilityFeature;
use App\Models\SpecializedEducationalSupport\Deficiency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

final class UpdateAccessibleEducationalMaterialTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/_tests/inclusive-radar/accessible-educational-materials';

    protected function setUp(): void
    {
        parent::setUp();

        Route::put(
            self::ENDPOINT.'/{material}',
            [AccessibleEducationalMaterialController::class, 'update'],
        )->middleware('web');
    }

    public function test_it_updates_material_stock_relations_and_inspection(): void
    {
        $user = User::factory()->create();
        $oldAudience = Deficiency::factory()->create();
        $newAudience = Deficiency::factory()->create();
        $oldFeature = AccessibilityFeature::factory()->create();
        $newFeature = AccessibilityFeature::factory()->create();
        $material = $this->physicalMaterial();
        $material->assignTargetAudience([$oldAudience->id]);
        $material->assignAccessibilityFeatures([$oldFeature->id]);

        $response = $this->actingAs($user)->put(
            self::ENDPOINT.'/'.$material->id,
            $this->payload([
                'name' => 'Livro em Braille atualizado',
                'quantity' => 5,
                'conservation_state' => ConservationState::REGULAR->value,
                'deficiencies' => [$newAudience->id],
                'accessibility_features' => [$newFeature->id],
                'inspection' => [
                    'date' => now()->toDateString(),
                    'type' => InspectionType::PERIODIC->value,
                    'description' => 'Revisão do material.',
                ],
            ]),
        );

        $response->assertRedirect(
            route('inclusive-radar.accessible-educational-materials.index'),
        );
        $response->assertSessionHas('success', 'Material atualizado com sucesso!');

        $this->assertDatabaseHas('accessible_educational_materials', [
            'id' => $material->id,
            'name' => 'Livro em Braille atualizado',
            'quantity' => 5,
            'quantity_available' => 5,
            'conservation_state' => ConservationState::REGULAR->value,
        ]);
        $this->assertDatabaseMissing('accessible_educational_material_deficiency', [
            'accessible_educational_material_id' => $material->id,
            'deficiency_id' => $oldAudience->id,
        ]);
        $this->assertDatabaseHas('accessible_educational_material_deficiency', [
            'accessible_educational_material_id' => $material->id,
            'deficiency_id' => $newAudience->id,
        ]);
        $this->assertDatabaseMissing('accessible_educational_material_accessibility', [
            'accessible_educational_material_id' => $material->id,
            'accessibility_feature_id' => $oldFeature->id,
        ]);
        $this->assertDatabaseHas('accessible_educational_material_accessibility', [
            'accessible_educational_material_id' => $material->id,
            'accessibility_feature_id' => $newFeature->id,
        ]);
        $this->assertDatabaseHas('inspections', [
            'inspectable_id' => $material->id,
            'inspectable_type' => 'accessible_educational_material',
            'state' => ConservationState::REGULAR->value,
            'type' => InspectionType::PERIODIC->value,
            'description' => 'Revisão do material.',
            'user_id' => $user->id,
        ]);
    }

    public function test_it_preserves_open_loans_when_recalculating_available_stock(): void
    {
        $user = User::factory()->create();
        $audience = Deficiency::factory()->create();
        $material = $this->physicalMaterial();
        $this->createOpenLoan($material, $user);

        $response = $this->actingAs($user)->put(
            self::ENDPOINT.'/'.$material->id,
            $this->payload([
                'quantity' => 4,
                'deficiencies' => [$audience->id],
            ]),
        );

        $response->assertRedirect(
            route('inclusive-radar.accessible-educational-materials.index'),
        );
        $this->assertDatabaseHas('accessible_educational_materials', [
            'id' => $material->id,
            'quantity' => 4,
            'quantity_available' => 3,
        ]);
    }

    public function test_it_rejects_stock_lower_than_the_number_of_open_loans(): void
    {
        $user = User::factory()->create();
        $audience = Deficiency::factory()->create();
        $material = $this->physicalMaterial();
        $this->createOpenLoan($material, $user);
        $this->createOpenLoan($material, $user);

        $response = $this->actingAs($user)
            ->from('/accessible-educational-materials/'.$material->id.'/edit')
            ->put(
                self::ENDPOINT.'/'.$material->id,
                $this->payload([
                    'quantity' => 1,
                    'deficiencies' => [$audience->id],
                ]),
            );

        $response->assertRedirect(
            '/accessible-educational-materials/'.$material->id.'/edit',
        );
        $response->assertSessionHas(
            'error',
            'Impossível reduzir estoque: existem 2 unidades emprestadas.',
        );
        $this->assertDatabaseHas('accessible_educational_materials', [
            'id' => $material->id,
            'quantity' => 2,
            'quantity_available' => 2,
        ]);
    }

    public function test_it_does_not_create_an_inspection_without_a_relevant_change(): void
    {
        $user = User::factory()->create();
        $audience = Deficiency::factory()->create();
        $material = $this->physicalMaterial();

        $response = $this->actingAs($user)->put(
            self::ENDPOINT.'/'.$material->id,
            $this->payload([
                'name' => 'Apenas novo nome',
                'deficiencies' => [$audience->id],
            ]),
        );

        $response->assertRedirect(
            route('inclusive-radar.accessible-educational-materials.index'),
        );
        $this->assertDatabaseCount('inspections', 0);
    }

    private function physicalMaterial(): AccessibleEducationalMaterial
    {
        $material = AccessibleEducationalMaterial::register(new CreateAccessibleEducationalMaterialDTO(
            name: 'Livro em Braille',
            isDigital: false,
            isLoanable: true,
            quantity: 2,
            assetCode: AssetCode::from('MPA-1001'),
            conservationState: ConservationState::GOOD,
        ));
        $material->save();

        return $material;
    }

    private function createOpenLoan(
        AccessibleEducationalMaterial $material,
        User $user,
    ): void {
        Loan::query()->create([
            'loanable_id' => $material->id,
            'loanable_type' => $material->getMorphClass(),
            'user_id' => $user->id,
            'loan_date' => now(),
            'due_date' => now()->addWeek(),
            'return_date' => null,
            'status' => LoanStatus::ACTIVE,
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_replace_recursive([
            'name' => 'Livro em Braille',
            'is_digital' => false,
            'is_loanable' => true,
            'quantity' => 2,
            'asset_code' => 'MPA-1001',
            'conservation_state' => ConservationState::GOOD->value,
            'status' => ResourceStatus::AVAILABLE->value,
            'is_active' => true,
            'deficiencies' => [],
            'accessibility_features' => [],
            'inspection' => [
                'date' => now()->toDateString(),
                'type' => InspectionType::PERIODIC->value,
                'description' => null,
                'images' => [],
            ],
        ], $overrides);
    }
}
