<?php

namespace Tests\Unit\InclusiveRadar;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\InclusiveRadar\AccessibilityFeature;
use App\Services\InclusiveRadar\AccessibilityFeatureService;

class AccessibilityFeatureServiceTest extends TestCase
{
    use RefreshDatabase;

    private AccessibilityFeatureService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new AccessibilityFeatureService();
    }

    /**
     * Objetivo: Garantir que o serviço consiga persistir um novo recurso de acessibilidade.
     * Cenário: Feliz.
     */
    public function test_it_can_store_a_feature()
    {
        // Arrange
        $data = [
            'name' => 'Audiodescrição',
            'description' => 'Recurso de apoio',
            'is_active' => true,
        ];

        // Act
        $feature = $this->service->store($data);

        // Assert
        $this->assertInstanceOf(AccessibilityFeature::class, $feature);

        $this->assertDatabaseHas('accessibility_features', [
            'name' => 'Audiodescrição',
            'description' => 'Recurso de apoio',
            'is_active' => true,
        ]);
    }

    /**
     * Objetivo: Validar a atualização dos campos de um recurso existente através do serviço.
     * Cenário: Feliz.
     */
    public function test_it_can_update_a_feature()
    {
        // Arrange
        $feature = AccessibilityFeature::factory()->create([
            'name' => 'Original',
            'is_active' => false,
        ]);

        $data = [
            'name' => 'Atualizado',
            'is_active' => true,
        ];

        // Act
        $updated = $this->service->update($feature, $data);

        // Assert
        $this->assertEquals('Atualizado', $updated->name);
        $this->assertTrue($updated->is_active);

        $this->assertDatabaseHas('accessibility_features', [
            'id' => $feature->id,
            'name' => 'Atualizado',
            'is_active' => true,
        ]);
    }

    /**
     * Objetivo: Garantir que o serviço remova fisicamente o registro do banco de dados.
     * Cenário: Feliz (Considerando que este modelo não usa SoftDeletes).
     */
    public function test_it_can_delete_a_feature()
    {
        // Arrange
        $feature = AccessibilityFeature::factory()->create();

        // Act
        $this->service->delete($feature);

        // Assert
        $this->assertDatabaseMissing('accessibility_features', [
            'id' => $feature->id,
        ]);
    }
}
