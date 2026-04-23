<?php

namespace Tests\Unit\InclusiveRadar;

use App\Enums\InclusiveRadar\ConservationState;
use App\Enums\InclusiveRadar\InspectionType;
use App\Enums\InclusiveRadar\ResourceStatus;
use App\Exceptions\BusinessRuleException;
use App\Models\InclusiveRadar\AssistiveTechnology;
use App\Models\InclusiveRadar\Loan;
use App\Models\SpecializedEducationalSupport\Deficiency;
use App\Services\InclusiveRadar\AssistiveTechnologyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssistiveTechnologyServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AssistiveTechnologyService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(AssistiveTechnologyService::class);
    }

    public function test_it_stores_an_assistive_technology_with_deficiencies_and_initial_inspection()
    {
        // Arrange
        $deficiencies = Deficiency::factory()->count(2)->create();

        $data = [
            'name' => 'Linha Braille Portátil',
            'is_digital' => false,
            'is_loanable' => true,
            'asset_code' => 'TA-1001',
            'quantity' => 3,
            'status' => ResourceStatus::AVAILABLE->value,
            'deficiencies' => $deficiencies->modelKeys(),
            'conservation_state' => ConservationState::GOOD->value,
            'inspection_type' => InspectionType::INITIAL->value,
            'inspection_date' => now()->toDateString(),
            'inspection_description' => 'Cadastro inicial',
        ];

        // Act
        $technology = $this->service->store($data);

        // Assert
        $this->assertInstanceOf(AssistiveTechnology::class, $technology);
        $this->assertSame(3, $technology->quantity);
        $this->assertSame(3, $technology->quantity_available);
        $this->assertCount(2, $technology->deficiencies);
        $this->assertDatabaseHas('inspections', [
            'inspectable_id' => $technology->id,
            'inspectable_type' => $technology->getMorphClass(),
            'type' => InspectionType::INITIAL->value,
        ]);
    }

    public function test_it_throws_exception_when_storing_without_deficiencies()
    {
        // Arrange
        $data = [
            'name' => 'Tecnologia Inválida',
            'is_digital' => false,
            'is_loanable' => false,
            'quantity' => 1,
            'status' => ResourceStatus::AVAILABLE->value,
            'deficiencies' => [],
            'conservation_state' => ConservationState::GOOD->value,
            'inspection_type' => InspectionType::INITIAL->value,
            'inspection_date' => now()->toDateString(),
        ];

        // Assert
        $this->expectException(BusinessRuleException::class);

        // Act
        $this->service->store($data);
    }

    public function test_it_throws_exception_when_storing_a_physical_assistive_technology_without_stock()
    {
        // Arrange
        $deficiency = Deficiency::factory()->create();

        $data = [
            'name' => 'Tecnologia Fisica Invalida',
            'is_digital' => false,
            'is_loanable' => false,
            'quantity' => 0,
            'status' => ResourceStatus::AVAILABLE->value,
            'deficiencies' => [$deficiency->id],
            'conservation_state' => ConservationState::GOOD->value,
            'inspection_type' => InspectionType::INITIAL->value,
            'inspection_date' => now()->toDateString(),
        ];

        // Assert
        $this->expectException(BusinessRuleException::class);
        $this->expectExceptionMessage('Para recursos físicos, a quantidade deve ser no mínimo 1.');

        // Act
        $this->service->store($data);
    }

    public function test_it_updates_assistive_technology_and_syncs_deficiencies()
    {
        // Arrange
        $oldDeficiency = Deficiency::factory()->create();
        $newDeficiencies = Deficiency::factory()->count(2)->create();

        $technology = AssistiveTechnology::factory()
            ->physical()
            ->available()
            ->create([
                'name' => 'TA Antiga',
                'quantity' => 2,
                'quantity_available' => 2,
                'status' => ResourceStatus::AVAILABLE,
            ]);

        $technology->deficiencies()->sync([$oldDeficiency->id]);

        $data = [
            'name' => 'TA Atualizada',
            'is_digital' => false,
            'is_loanable' => true,
            'quantity' => 6,
            'status' => ResourceStatus::AVAILABLE->value,
            'deficiencies' => $newDeficiencies->modelKeys(),
            'conservation_state' => ConservationState::REGULAR->value,
            'inspection_type' => InspectionType::PERIODIC->value,
            'inspection_date' => now()->toDateString(),
            'inspection_description' => 'Aferição de rotina',
        ];

        // Act
        $updated = $this->service->update($technology, $data);

        // Assert
        $this->assertSame('TA Atualizada', $updated->name);
        $this->assertSame(6, $updated->quantity);
        $this->assertSame(6, $updated->quantity_available);
        $this->assertEqualsCanonicalizing(
            $newDeficiencies->modelKeys(),
            $updated->deficiencies->modelKeys()
        );
    }

    public function test_it_throws_exception_when_changing_status_with_active_loans()
    {
        // Arrange
        $deficiency = Deficiency::factory()->create();

        $technology = AssistiveTechnology::factory()
            ->physical()
            ->available()
            ->create([
                'status' => ResourceStatus::AVAILABLE,
                'quantity' => 2,
                'quantity_available' => 2,
            ]);

        $technology->deficiencies()->sync([$deficiency->id]);

        Loan::factory()
            ->forAssistiveTechnology($technology)
            ->create([
                'status' => 'active',
                'return_date' => null,
            ]);

        $data = [
            'name' => $technology->name,
            'is_digital' => false,
            'is_loanable' => true,
            'quantity' => 2,
            'status' => ResourceStatus::UNDER_MAINTENANCE->value,
            'deficiencies' => [$deficiency->id],
            'conservation_state' => ConservationState::GOOD->value,
            'inspection_date' => now()->toDateString(),
        ];

        // Assert
        $this->expectException(BusinessRuleException::class);

        // Act
        $this->service->update($technology, $data);
    }

    public function test_it_throws_exception_when_deleting_assistive_technology_with_active_loans()
    {
        // Arrange
        $technology = AssistiveTechnology::factory()
            ->physical()
            ->available()
            ->create();

        Loan::factory()
            ->forAssistiveTechnology($technology)
            ->create([
                'status' => 'active',
                'return_date' => null,
            ]);

        // Assert
        $this->expectException(BusinessRuleException::class);
        $this->expectExceptionMessage('Não é possível excluir um item com empréstimos ativos.');

        // Act
        $this->service->delete($technology);
    }

    public function test_it_deletes_assistive_technology_when_there_are_no_active_loans()
    {
        // Arrange
        $technology = AssistiveTechnology::factory()
            ->physical()
            ->available()
            ->create();

        // Act
        $this->service->delete($technology);

        // Assert
        $this->assertSoftDeleted('assistive_technologies', [
            'id' => $technology->id,
        ]);
    }

    public function test_it_throws_exception_when_storing_digital_loanable_assistive_technology_without_stock()
    {
        // Arrange
        $deficiency = Deficiency::factory()->create();

        $data = [
            'name' => 'TA Digital Emprestavel',
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
        $this->expectExceptionMessage('Recursos marcados como emprestáveis devem ter quantidade maior que zero.');

        // Act
        $this->service->store($data);
    }

    public function test_it_throws_exception_when_available_quantity_is_greater_than_total_quantity_for_assistive_technology()
    {
        // Arrange
        $deficiency = Deficiency::factory()->create();

        $data = [
            'name' => 'TA Com Estoque Invalido',
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
