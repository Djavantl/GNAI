<?php

namespace Tests\Unit\InclusiveRadar;

use App\Exceptions\InclusiveRadar\CannotDeleteLinkedBarrierException;
use App\Models\InclusiveRadar\Barrier;
use App\Models\InclusiveRadar\Institution;
use App\Models\InclusiveRadar\Location;
use App\Services\InclusiveRadar\LocationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class LocationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected LocationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new LocationService();
    }

    /**
     * Objetivo: Garantir que um local pode ser criado normalmente.
     * Cenário: Feliz.
     */
    public function test_it_stores_a_location()
    {
        // Arrange
        $institution = Institution::factory()->create();
        $data = Location::factory()
            ->make(['institution_id' => $institution->id])
            ->toArray();

        // Act
        $result = $this->service->store($data);

        // Assert
        $this->assertInstanceOf(Location::class, $result);
        $this->assertDatabaseHas('locations', [
            'id' => $result->id
        ]);
    }

    /**
     * Objetivo: Atualizar dados básicos do local.
     * Cenário: Feliz.
     */
    public function test_it_updates_location_data()
    {
        // Arrange
        $location = Location::factory()->create(['name' => 'Antigo']);

        // Act
        $result = $this->service->update($location, [
            'name' => 'Novo'
        ]);

        // Assert
        $this->assertEquals('Novo', $result->name);
        $this->assertDatabaseHas('locations', [
            'id' => $location->id,
            'name' => 'Novo'
        ]);
    }

    /**
     * Objetivo: Impedir desativação com barreiras não resolvidas.
     * Cenário: Triste (Regra de Negócio).
     */
    public function test_it_throws_exception_when_deactivating_with_unresolved_barriers()
    {
        // Arrange
        $location = Location::factory()->create(['is_active' => true]);

        Barrier::factory()->create([
            'location_id' => $location->id,
            'resolved_at' => null
        ]);

        // Assert
        $this->expectException(ValidationException::class);

        // Act
        $this->service->update($location, [
            'is_active' => false
        ]);
    }

    /**
     * Objetivo: Permitir desativação quando não há barreiras abertas.
     * Cenário: Feliz.
     */
    public function test_it_deactivates_location_when_no_unresolved_barriers()
    {
        // Arrange
        $location = Location::factory()->create(['is_active' => true]);

        // Act
        $this->service->update($location, [
            'is_active' => false
        ]);

        // Assert
        $this->assertFalse($location->fresh()->is_active);
    }

    /**
     * Objetivo: Impedir exclusão quando houver barreiras não resolvidas.
     * Cenário: Triste.
     */
    public function test_it_throws_exception_when_deleting_with_unresolved_barriers()
    {
        // Arrange
        $location = Location::factory()->create();

        Barrier::factory()->create([
            'location_id' => $location->id,
            'resolved_at' => null
        ]);

        // Assert
        $this->expectException(CannotDeleteLinkedBarrierException::class);

        // Act
        $this->service->delete($location);
    }

    /**
     * Objetivo: Permitir exclusão quando não houver pendências.
     * Cenário: Feliz.
     */
    public function test_it_deletes_location_successfully()
    {
        // Arrange
        $location = Location::factory()->create();

        // Act
        $this->service->delete($location);

        // Assert
        $this->assertSoftDeleted('locations', [
            'id' => $location->id
        ]);
    }

    /**
     * Objetivo: Garantir que a regra de bloqueio só se aplica à desativação.
     * Cenário: Local com barreiras abertas, mas alterando apenas o nome.
     */
    public function test_it_allows_updating_name_even_with_unresolved_barriers()
    {
        // Arrange
        $location = Location::factory()->create(['name' => 'Original', 'is_active' => true]);
        Barrier::factory()->create(['location_id' => $location->id, 'resolved_at' => null]);

        // Act
        $result = $this->service->update($location, ['name' => 'Novo Nome']);

        // Assert
        $this->assertEquals('Novo Nome', $result->name);
        $this->assertTrue($result->is_active);
    }

    /**
     * Objetivo: Validar que a exclusão é permitida se todas as barreiras estiverem resolvidas.
     * Cenário: Feliz.
     */
    public function test_it_allows_deletion_when_all_barriers_are_resolved()
    {
        // Arrange
        $location = Location::factory()->create();
        Barrier::factory()->create([
            'location_id' => $location->id,
            'resolved_at' => now()
        ]);

        // Act
        $this->service->delete($location);

        // Assert
        $this->assertSoftDeleted('locations', ['id' => $location->id]);
    }

    /**
     * Objetivo: Garantir que reativar um local nunca é bloqueado.
     */
    public function test_it_allows_reactivation_regardless_of_barriers()
    {
        // Arrange
        $location = Location::factory()->create(['is_active' => false]);

        // Act
        $result = $this->service->update($location, ['is_active' => true]);

        // Assert
        $this->assertTrue($result->fresh()->is_active);
    }
}
