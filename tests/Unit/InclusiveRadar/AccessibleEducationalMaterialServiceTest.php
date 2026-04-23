<?php

namespace Tests\Unit\InclusiveRadar;

use App\Enums\InclusiveRadar\ConservationState;
use App\Enums\InclusiveRadar\InspectionType;
use App\Enums\InclusiveRadar\ResourceStatus;
use App\Exceptions\BusinessRuleException;
use App\Models\InclusiveRadar\AccessibleEducationalMaterial;
use App\Models\InclusiveRadar\AccessibilityFeature;
use App\Models\InclusiveRadar\Loan;
use App\Models\SpecializedEducationalSupport\Deficiency;
use App\Services\InclusiveRadar\AccessibleEducationalMaterialService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessibleEducationalMaterialServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AccessibleEducationalMaterialService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(AccessibleEducationalMaterialService::class);
    }

    public function test_it_stores_an_accessible_educational_material_with_relations_and_initial_inspection()
    {
        // Arrange
        $deficiencies = Deficiency::factory()->count(2)->create();
        $features = AccessibilityFeature::factory()->count(2)->create();

        $data = [
            'name' => 'Material Tátil Inclusivo',
            'is_digital' => false,
            'is_loanable' => true,
            'notes' => 'Material para alfabetização',
            'asset_code' => 'PAT-9001',
            'quantity' => 5,
            'status' => ResourceStatus::AVAILABLE->value,
            'is_active' => true,
            'deficiencies' => $deficiencies->modelKeys(),
            'accessibility_features' => $features->modelKeys(),
            'conservation_state' => ConservationState::GOOD->value,
            'inspection_type' => InspectionType::INITIAL->value,
            'inspection_date' => now()->toDateString(),
            'inspection_description' => 'Entrada no acervo',
        ];

        // Act
        $material = $this->service->store($data);

        // Assert
        $this->assertInstanceOf(AccessibleEducationalMaterial::class, $material);
        $this->assertSame(5, $material->quantity);
        $this->assertSame(5, $material->quantity_available);
        $this->assertCount(2, $material->deficiencies);
        $this->assertCount(2, $material->accessibilityFeatures);
        $this->assertDatabaseHas('inspections', [
            'inspectable_id' => $material->id,
            'inspectable_type' => $material->getMorphClass(),
            'type' => InspectionType::INITIAL->value,
        ]);
    }

    public function test_it_throws_exception_when_storing_a_physical_material_without_stock()
    {
        // Arrange
        $deficiency = Deficiency::factory()->create();

        $data = [
            'name' => 'Material Inválido',
            'is_digital' => false,
            'is_loanable' => true,
            'quantity' => 0,
            'status' => ResourceStatus::AVAILABLE->value,
            'deficiencies' => [$deficiency->id],
            'conservation_state' => ConservationState::GOOD->value,
            'inspection_type' => InspectionType::INITIAL->value,
            'inspection_date' => now()->toDateString(),
        ];

        // Assert
        $this->expectException(BusinessRuleException::class);

        // Act
        $this->service->store($data);
    }

    public function test_it_updates_material_and_syncs_relations()
    {
        // Arrange
        $oldDeficiency = Deficiency::factory()->create();
        $newDeficiencies = Deficiency::factory()->count(2)->create();
        $oldFeature = AccessibilityFeature::factory()->create();
        $newFeatures = AccessibilityFeature::factory()->count(2)->create();

        $material = AccessibleEducationalMaterial::factory()
            ->physical()
            ->available()
            ->create([
                'name' => 'Material Original',
                'quantity' => 4,
                'quantity_available' => 4,
                'status' => ResourceStatus::AVAILABLE,
                'conservation_state' => ConservationState::GOOD,
            ]);

        $material->deficiencies()->sync([$oldDeficiency->id]);
        $material->accessibilityFeatures()->sync([$oldFeature->id]);

        $data = [
            'name' => 'Material Atualizado',
            'is_digital' => false,
            'is_loanable' => true,
            'quantity' => 7,
            'status' => ResourceStatus::AVAILABLE->value,
            'deficiencies' => $newDeficiencies->modelKeys(),
            'accessibility_features' => $newFeatures->modelKeys(),
            'conservation_state' => ConservationState::REGULAR->value,
            'inspection_type' => InspectionType::PERIODIC->value,
            'inspection_date' => now()->toDateString(),
            'inspection_description' => 'Revisão periódica',
        ];

        // Act
        $updated = $this->service->update($material, $data);

        // Assert
        $this->assertSame('Material Atualizado', $updated->name);
        $this->assertSame(7, $updated->quantity);
        $this->assertSame(7, $updated->quantity_available);
        $this->assertEqualsCanonicalizing(
            $newDeficiencies->modelKeys(),
            $updated->deficiencies->modelKeys()
        );
        $this->assertEqualsCanonicalizing(
            $newFeatures->modelKeys(),
            $updated->accessibilityFeatures->modelKeys()
        );
    }

    public function test_it_throws_exception_when_changing_status_with_active_loans()
    {
        // Arrange
        $deficiency = Deficiency::factory()->create();

        $material = AccessibleEducationalMaterial::factory()
            ->physical()
            ->available()
            ->create([
                'status' => ResourceStatus::AVAILABLE,
                'quantity' => 3,
                'quantity_available' => 3,
            ]);

        $material->deficiencies()->sync([$deficiency->id]);

        Loan::factory()
            ->forAccessibleEducationalMaterial($material)
            ->create([
                'status' => 'active',
                'return_date' => null,
            ]);

        $data = [
            'name' => $material->name,
            'is_digital' => false,
            'is_loanable' => true,
            'quantity' => 3,
            'status' => ResourceStatus::UNAVAILABLE->value,
            'deficiencies' => [$deficiency->id],
            'conservation_state' => ConservationState::GOOD->value,
            'inspection_date' => now()->toDateString(),
        ];

        // Assert
        $this->expectException(BusinessRuleException::class);

        // Act
        $this->service->update($material, $data);
    }

    public function test_it_throws_exception_when_deleting_material_with_active_loans()
    {
        // Arrange
        $material = AccessibleEducationalMaterial::factory()
            ->physical()
            ->available()
            ->create();

        Loan::factory()
            ->forAccessibleEducationalMaterial($material)
            ->create([
                'status' => 'active',
                'return_date' => null,
            ]);

        // Assert
        $this->expectException(BusinessRuleException::class);

        // Act
        $this->service->delete($material);
    }

    public function test_it_deletes_material_when_there_are_no_active_loans()
    {
        // Arrange
        $material = AccessibleEducationalMaterial::factory()
            ->physical()
            ->available()
            ->create();

        // Act
        $this->service->delete($material);

        // Assert
        $this->assertSoftDeleted('accessible_educational_materials', [
            'id' => $material->id,
        ]);
    }

    public function test_it_throws_exception_when_storing_material_with_empty_deficiencies_list()
    {
        // Arrange
        $data = [
            'name' => 'Material Sem Publico',
            'is_digital' => true,
            'is_loanable' => false,
            'status' => ResourceStatus::AVAILABLE->value,
            'deficiencies' => [],
            'conservation_state' => ConservationState::GOOD->value,
            'inspection_type' => InspectionType::INITIAL->value,
            'inspection_date' => now()->toDateString(),
        ];

        // Assert
        $this->expectException(BusinessRuleException::class);
        $this->expectExceptionMessage('Selecione pelo menos um público-alvo.');

        // Act
        $this->service->store($data);
    }

    public function test_it_throws_exception_when_storing_digital_loanable_material_without_stock()
    {
        // Arrange
        $deficiency = Deficiency::factory()->create();

        $data = [
            'name' => 'Material Digital Emprestavel',
            'is_digital' => true,
            'is_loanable' => true,
            'quantity' => 0,
            'status' => ResourceStatus::AVAILABLE->value,
            'deficiencies' => [$deficiency->id],
            'conservation_state' => ConservationState::GOOD->value,
            'inspection_type' => InspectionType::INITIAL->value,
            'inspection_date' => now()->toDateString(),
        ];

        // Assert
        $this->expectException(BusinessRuleException::class);
        $this->expectExceptionMessage('Materiais marcados como emprestáveis devem ter quantidade maior que zero.');

        // Act
        $this->service->store($data);
    }

    public function test_it_throws_exception_when_available_quantity_is_greater_than_total_quantity()
    {
        // Arrange
        $deficiency = Deficiency::factory()->create();

        $data = [
            'name' => 'Material Com Estoque Invalido',
            'is_digital' => false,
            'is_loanable' => false,
            'quantity' => 2,
            'quantity_available' => 3,
            'status' => ResourceStatus::AVAILABLE->value,
            'deficiencies' => [$deficiency->id],
            'conservation_state' => ConservationState::GOOD->value,
            'inspection_type' => InspectionType::INITIAL->value,
            'inspection_date' => now()->toDateString(),
        ];

        // Assert
        $this->expectException(BusinessRuleException::class);
        $this->expectExceptionMessage('A quantidade disponível (3) não pode ser maior que a quantidade total (2).');

        // Act
        $this->service->store($data);
    }
}
