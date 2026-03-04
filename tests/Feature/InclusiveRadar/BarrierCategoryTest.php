<?php

namespace Tests\Feature\InclusiveRadar;

use App\Models\User;
use App\Models\InclusiveRadar\BarrierCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BarrierCategoryTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        // Arrange (Global)
        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->regularUser = User::factory()->create(['is_admin' => false]);
    }

    public function test_guest_cannot_access_index()
    {
        // Act
        $response = $this->get(route('inclusive-radar.barrier-categories.index'));
        // Assert
        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_index()
    {
        // Act
        $response = $this->actingAs($this->regularUser)->get(route('inclusive-radar.barrier-categories.index'));
        // Assert
        $response->assertForbidden();
    }

    public function test_admin_can_access_index()
    {
        // Act
        $response = $this->actingAs($this->admin)->get(route('inclusive-radar.barrier-categories.index'));
        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.inclusive-radar.barrier-categories.index');
    }

    public function test_index_returns_partial_when_ajax()
    {
        // Arrange
        BarrierCategory::factory()->count(2)->create();
        // Act
        $response = $this->actingAs($this->admin)->get(route('inclusive-radar.barrier-categories.index'), [
            'HTTP_X-Requested-With' => 'XMLHttpRequest'
        ]);
        // Assert
        $response->assertOk();
        $this->assertStringContainsString('<table', $response->getContent());
        $response->assertDontSee('<!DOCTYPE html>');
    }

    public function test_index_returns_full_view_when_not_ajax()
    {
        // Arrange
        BarrierCategory::factory()->count(1)->create();
        // Act
        $response = $this->actingAs($this->admin)->get(route('inclusive-radar.barrier-categories.index'));
        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.inclusive-radar.barrier-categories.index');
    }

    public function test_guest_cannot_access_create()
    {
        // Act
        $response = $this->get(route('inclusive-radar.barrier-categories.create'));
        // Assert
        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_create()
    {
        // Act
        $response = $this->actingAs($this->regularUser)->get(route('inclusive-radar.barrier-categories.create'));
        // Assert
        $response->assertForbidden();
    }

    public function test_admin_can_access_create_page()
    {
        // Act
        $response = $this->actingAs($this->admin)->get(route('inclusive-radar.barrier-categories.create'));
        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.inclusive-radar.barrier-categories.create');
    }

    public function test_guest_cannot_access_edit()
    {
        // Arrange
        $category = BarrierCategory::factory()->create();
        // Act
        $response = $this->get(route('inclusive-radar.barrier-categories.edit', $category));
        // Assert
        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_edit()
    {
        // Arrange
        $category = BarrierCategory::factory()->create();
        // Act
        $response = $this->actingAs($this->regularUser)->get(route('inclusive-radar.barrier-categories.edit', $category));
        // Assert
        $response->assertForbidden();
    }

    public function test_admin_can_access_edit_page()
    {
        // Arrange
        $category = BarrierCategory::factory()->create();
        // Act
        $response = $this->actingAs($this->admin)->get(route('inclusive-radar.barrier-categories.edit', $category));
        // Assert
        $response->assertOk();
        $response->assertViewHas('barrierCategory', $category);
    }

    public function test_guest_cannot_view_category()
    {
        // Arrange
        $category = BarrierCategory::factory()->create();
        // Act
        $response = $this->get(route('inclusive-radar.barrier-categories.show', $category));
        // Assert
        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_view_category()
    {
        // Arrange
        $category = BarrierCategory::factory()->create();
        // Act
        $response = $this->actingAs($this->regularUser)->get(route('inclusive-radar.barrier-categories.show', $category));
        // Assert
        $response->assertForbidden();
    }

    public function test_admin_can_view_category_details()
    {
        // Arrange
        $category = BarrierCategory::factory()->create();
        // Act
        $response = $this->actingAs($this->admin)->get(route('inclusive-radar.barrier-categories.show', $category));
        // Assert
        $response->assertOk();
        $response->assertViewHas('barrierCategory', $category);
    }

    public function test_guest_cannot_store()
    {
        // Act
        $response = $this->post(route('inclusive-radar.barrier-categories.store'), ['name' => 'Teste']);
        // Assert
        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_store()
    {
        // Act
        $response = $this->actingAs($this->regularUser)->post(route('inclusive-radar.barrier-categories.store'), ['name' => 'Teste']);
        // Assert
        $response->assertForbidden();
    }

    public function test_admin_can_store()
    {
        // Arrange
        $data = ['name' => 'Nova Categoria'];
        // Act
        $response = $this->actingAs($this->admin)->post(route('inclusive-radar.barrier-categories.store'), $data);
        // Assert
        $response->assertRedirect(route('inclusive-radar.barrier-categories.index'));
        $this->assertDatabaseHas('barrier_categories', ['name' => 'Nova Categoria']);
    }

    public function test_store_requires_unique_name()
    {
        // Arrange
        BarrierCategory::factory()->create(['name' => 'Existente']);
        // Act
        $response = $this->actingAs($this->admin)->post(route('inclusive-radar.barrier-categories.store'), ['name' => 'Existente']);
        // Assert
        $response->assertSessionHasErrors('name');
    }

    public function test_guest_cannot_update()
    {
        // Arrange
        $category = BarrierCategory::factory()->create();
        // Act
        $response = $this->put(route('inclusive-radar.barrier-categories.update', $category), ['name' => 'Novo']);
        // Assert
        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_update()
    {
        // Arrange
        $category = BarrierCategory::factory()->create();
        // Act
        $response = $this->actingAs($this->regularUser)->put(route('inclusive-radar.barrier-categories.update', $category), ['name' => 'Novo']);
        // Assert
        $response->assertForbidden();
    }

    public function test_admin_can_update()
    {
        // Arrange
        $category = BarrierCategory::factory()->create(['name' => 'Original']);
        // Act
        $response = $this->actingAs($this->admin)->put(route('inclusive-radar.barrier-categories.update', $category), ['name' => 'Atualizado']);
        // Assert
        $response->assertRedirect(route('inclusive-radar.barrier-categories.index'));
        $this->assertDatabaseHas('barrier_categories', ['id' => $category->id, 'name' => 'Atualizado']);
    }

    public function test_update_allows_same_name_on_current_category()
    {
        // Arrange
        $category = BarrierCategory::factory()->create(['name' => 'NomeUnico']);
        // Act
        $response = $this->actingAs($this->admin)->put(route('inclusive-radar.barrier-categories.update', $category), ['name' => 'NomeUnico']);
        // Assert
        $response->assertRedirect(route('inclusive-radar.barrier-categories.index'));
    }

    public function test_update_requires_unique_name_excluding_self()
    {
        // Arrange
        BarrierCategory::factory()->create(['name' => 'Outra']);
        $category = BarrierCategory::factory()->create(['name' => 'Minha']);
        // Act
        $response = $this->actingAs($this->admin)->put(route('inclusive-radar.barrier-categories.update', $category), ['name' => 'Outra']);
        // Assert
        $response->assertSessionHasErrors('name');
    }

    public function test_guest_cannot_delete()
    {
        // Arrange
        $category = BarrierCategory::factory()->create();
        // Act
        $response = $this->delete(route('inclusive-radar.barrier-categories.destroy', $category));
        // Assert
        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_delete()
    {
        // Arrange
        $category = BarrierCategory::factory()->create();
        // Act
        $response = $this->actingAs($this->regularUser)->delete(route('inclusive-radar.barrier-categories.destroy', $category));
        // Assert
        $response->assertForbidden();
    }

    public function test_admin_can_delete()
    {
        // Arrange
        $category = BarrierCategory::factory()->create();
        // Act
        $response = $this->actingAs($this->admin)->delete(route('inclusive-radar.barrier-categories.destroy', $category));
        // Assert
        $response->assertRedirect(route('inclusive-radar.barrier-categories.index'));
        $this->assertSoftDeleted('barrier_categories', ['id' => $category->id]);
    }

    public function test_index_can_filter_by_name()
    {
        // Arrange
        BarrierCategory::factory()->create(['name' => 'BuscaEspecifica']);
        BarrierCategory::factory()->create(['name' => 'RegistroInvisivel']);

        // Act
        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.barrier-categories.index', ['name' => 'BuscaEspecifica']));

        // Assert
        $response->assertOk();
        $response->assertSee('BuscaEspecifica');

        $response->assertDontSee('RegistroInvisivel');
    }

    public function test_index_can_filter_by_active_status()
    {
        // Arrange
        BarrierCategory::factory()->active()->create(['name' => 'CategoriaAtiva']);
        BarrierCategory::factory()->inactive()->create(['name' => 'CategoriaInativa']);

        // Act & Assert
        $this->actingAs($this->admin)
            ->get(route('inclusive-radar.barrier-categories.index', ['is_active' => '1']))
            ->assertOk()
            ->assertSee('CategoriaAtiva')
            ->assertDontSee('CategoriaInativa');

        // Act & Assert
        $this->actingAs($this->admin)
            ->get(route('inclusive-radar.barrier-categories.index', ['is_active' => '0']))
            ->assertOk()
            ->assertSee('CategoriaInativa')
            ->assertDontSee('CategoriaAtiva');
    }
}
