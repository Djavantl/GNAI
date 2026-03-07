<?php

namespace Tests\Unit\InclusiveRadar;

use App\Enums\InclusiveRadar\BarrierStatus;
use App\Models\InclusiveRadar\Inspection;
use Tests\TestCase;
use App\Models\InclusiveRadar\BarrierCategory;
use App\Models\InclusiveRadar\Barrier;
use App\Services\InclusiveRadar\BarrierCategoryService;
use App\Exceptions\InclusiveRadar\CannotDeleteLinkedBarrierException;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BarrierCategoryServiceTest extends TestCase
{
    use RefreshDatabase;

    protected BarrierCategoryService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(BarrierCategoryService::class);
    }

    /**
     * Objetivo: Garantir que uma nova categoria de barreira seja persistida corretamente.
     * Cenário: Feliz.
     */
    public function test_it_stores_a_category()
    {
        // Arrange
        $data = ['name' => 'Categoria Teste'];

        // Act
        $category = $this->service->store($data);

        // Assert
        $this->assertDatabaseHas('barrier_categories', [
            'name' => 'Categoria Teste'
        ]);
        $this->assertInstanceOf(BarrierCategory::class, $category);
    }

    /**
     * Objetivo: Verificar se os dados de uma categoria existente são atualizados.
     * Cenário: Feliz.
     */
    public function test_it_updates_a_category()
    {
        // Arrange
        $category = BarrierCategory::factory()->create([
            'name' => 'Antigo'
        ]);

        // Act
        $updated = $this->service->update($category, [
            'name' => 'Novo Nome'
        ]);

        // Assert
        $this->assertEquals('Novo Nome', $updated->name);
        $this->assertDatabaseHas('barrier_categories', [
            'id' => $category->id,
            'name' => 'Novo Nome'
        ]);
    }

    /**
     * Objetivo: Permitir a exclusão de uma categoria que não possui nenhum vínculo.
     * Cenário: Feliz.
     */
    public function test_it_deletes_category_when_no_barriers_exist()
    {
        // Arrange
        $category = BarrierCategory::factory()->create();

        // Act
        $this->service->delete($category);

        // Assert
        $this->assertSoftDeleted('barrier_categories', [
            'id' => $category->id
        ]);
    }

    /**
     * Objetivo: Impedir a exclusão de categoria que possua barreiras vinculadas (independente de status).
     * Cenário: Triste (Violação de integridade).
     */
    public function test_it_throws_exception_when_barrier_has_no_status()
    {
        // Arrange
        $category = BarrierCategory::factory()->create();

        Barrier::factory()->create([
            'barrier_category_id' => $category->id
        ]);

        // Assert
        $this->expectException(CannotDeleteLinkedBarrierException::class);

        // Act
        $this->service->delete($category);
    }

    /**
     * Objetivo: Bloquear a exclusão caso existam inspeções com status impeditivo (ex: Identificada).
     * Cenário: Triste (Regra de Negócio).
     */
    public function test_it_throws_exception_when_barrier_status_does_not_allow_deletion()
    {
        // Arrange
        $category = BarrierCategory::factory()->create();

        $barrier = Barrier::factory()->create([
            'barrier_category_id' => $category->id
        ]);

        Inspection::factory()
            ->forBarrier($barrier)
            ->create([
                'status' => BarrierStatus::IDENTIFIED->value
            ]);

        // Assert
        $this->expectException(CannotDeleteLinkedBarrierException::class);

        // Act
        $this->service->delete($category);
    }

    /**
     * Objetivo: Permitir a exclusão quando as barreiras vinculadas estão todas resolvidas.
     * Cenário: Feliz.
     */
    public function test_it_deletes_when_all_barriers_allow_deletion()
    {
        // Arrange
        $category = BarrierCategory::factory()->create();

        $barrier = Barrier::factory()->create([
            'barrier_category_id' => $category->id
        ]);

        Inspection::factory()
            ->forBarrier($barrier)
            ->create([
                'status' => BarrierStatus::RESOLVED->value
            ]);

        // Act
        $this->service->delete($category);

        // Assert
        $this->assertSoftDeleted('barrier_categories', [
            'id' => $category->id
        ]);
    }
}
