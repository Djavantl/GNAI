<?php

namespace Tests\Unit\InclusiveRadar;

use App\Enums\InclusiveRadar\BarrierStatus;
use App\Exceptions\InclusiveRadar\CannotDeleteLinkedBarrierException;
use App\Models\InclusiveRadar\Barrier;
use App\Models\InclusiveRadar\Inspection;
use App\Models\InclusiveRadar\Institution;
use App\Models\InclusiveRadar\Location;
use App\Services\InclusiveRadar\InstitutionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class InstitutionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected InstitutionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new InstitutionService();
    }

    /** * Objetivo: Validar que apenas uma instituição pode existir no sistema.
     * Cenário: Feliz (Primeiro cadastro).
     */
    public function test_it_stores_an_institution_when_none_exists()
    {
        // Arrange
        $data = Institution::factory()->make()->toArray();

        // Act
        $result = $this->service->store($data);

        // Assert
        $this->assertInstanceOf(Institution::class, $result);
        $this->assertDatabaseHas('institutions', ['name' => $data['name']]);
    }

    /** * Objetivo: Impedir a criação de múltiplas instituições.
     * Cenário: Triste (Duplicidade).
     */
    public function test_it_returns_null_if_an_institution_already_exists()
    {
        // Arrange
        Institution::factory()->create();
        $newData = Institution::factory()->make(['name' => 'Segunda Inst'])->toArray();

        // Act
        $result = $this->service->store($newData);

        // Assert
        $this->assertNull($result);
        $this->assertEquals(1, Institution::count());
    }

    /** * Objetivo: Garantir que a atualização de dados básicos funciona.
     * Cenário: Feliz.
     */
    public function test_it_updates_institution_data()
    {
        // Arrange
        $institution = Institution::factory()->create(['name' => 'Nome Antigo']);
        $updateData = ['name' => 'Nome Novo'];

        // Act
        $result = $this->service->update($institution, $updateData);

        // Assert
        $this->assertEquals('Nome Novo', $result->name);
        $this->assertDatabaseHas('institutions', ['id' => $institution->id, 'name' => 'Nome Novo']);
    }

    /** * Objetivo: Impedir desativação de instituição com barreiras em aberto.
     * Cenário: Triste (Regra de Negócio).
     */
    public function test_it_throws_exception_when_deactivating_with_unresolved_barriers()
    {
        // Arrange
        $institution = Institution::factory()->create(['is_active' => true]);
        Barrier::factory()->create([
            'institution_id' => $institution->id,
            'resolved_at' => null
        ]);

        // Assert
        $this->expectException(ValidationException::class);

        // Act
        $this->service->update($institution, ['is_active' => false]);
    }

    /** * Objetivo: Verificar o efeito cascata de desativação nos locais.
     * Cenário: Feliz (Consistência de dados).
     */
    public function test_it_deactivates_all_locations_when_institution_is_deactivated()
    {
        // Arrange
        $institution = Institution::factory()->create(['is_active' => true]);
        $locations = Location::factory()->count(3)->create([
            'institution_id' => $institution->id,
            'is_active' => true
        ]);

        // Act
        $this->service->update($institution, ['is_active' => false]);

        // Assert
        $this->assertFalse($institution->fresh()->is_active);
        foreach ($locations as $location) {
            $this->assertFalse($location->fresh()->is_active);
        }
    }

    /** * Objetivo: Impedir a remoção se houver barreiras com status impeditivo.
     * Cenário: Triste (Exceção customizada).
     */
    public function test_it_throws_exception_if_deleting_with_active_barriers()
    {
        // Arrange
        $institution = Institution::factory()->create();

        /** * Aqui simulamos a lógica do seu service:
         * Se latestStatus() for null ou allowsDeletion() for false, lança erro.
         * Como latestStatus() busca inspeções, vamos criar uma barreira sem inspeção.
         */
        Barrier::factory()->create(['institution_id' => $institution->id]);

        // Assert
        $this->expectException(CannotDeleteLinkedBarrierException::class);

        // Act
        $this->service->delete($institution);
    }

    /** * Objetivo: Garantir que o Soft Delete da instituição funciona.
     * Cenário: Feliz.
     */
    public function test_it_soft_deletes_institution_successfully()
    {
        // Arrange
        $institution = Institution::factory()->create();

        // Act
        $this->service->delete($institution);

        // Assert
        $this->assertSoftDeleted($institution);
    }

    /**
     * Objetivo: Permitir exclusão quando todas as barreiras permitem deleção.
     * Cenário: Feliz (status permite exclusão).
     */
    public function test_it_deletes_when_barriers_allow_deletion()
    {
        // Arrange
        $institution = Institution::factory()->create();

        $barrier = Barrier::factory()->create([
            'institution_id' => $institution->id
        ]);

        Inspection::factory()
            ->forBarrier($barrier)
            ->state([
                'status' => BarrierStatus::RESOLVED->value,
                'inspection_date' => now()->addMinute(),
            ])
            ->create();

        // Act
        $this->service->delete($institution);

        // Assert
        $this->assertSoftDeleted('institutions', [
            'id' => $institution->id
        ]);
    }
}
