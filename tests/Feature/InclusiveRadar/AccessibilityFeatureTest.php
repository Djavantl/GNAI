<?php

namespace Tests\Feature\InclusiveRadar;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\InclusiveRadar\AccessibilityFeature;

class AccessibilityFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Arrange
        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->regularUser = User::factory()->create(['is_admin' => false]);
    }

    public function test_guest_cannot_access_index()
    {
        // Act
        $response = $this->get(route('inclusive-radar.accessibility-features.index'));

        // Assert
        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_index()
    {
        // Act
        $response = $this->actingAs($this->regularUser)
            ->get(route('inclusive-radar.accessibility-features.index'));

        // Assert
        $response->assertForbidden();
    }

    public function test_admin_can_access_index()
    {
        // Act
        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.accessibility-features.index'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.inclusive-radar.accessibility-features.index');
    }

    public function test_index_returns_partial_when_ajax()
    {
        // Arrange
        AccessibilityFeature::factory()->count(2)->create();

        // Act
        $response = $this->actingAs($this->admin)
            ->get(
                route('inclusive-radar.accessibility-features.index'),
                ['HTTP_X-Requested-With' => 'XMLHttpRequest']
            );

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.inclusive-radar.accessibility-features.partials.table');
    }

    public function test_guest_cannot_access_create()
    {
        // Act
        $response = $this->get(route('inclusive-radar.accessibility-features.create'));

        // Assert
        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_create()
    {
        // Act
        $response = $this->actingAs($this->regularUser)
            ->get(route('inclusive-radar.accessibility-features.create'));

        // Assert
        $response->assertForbidden();
    }

    public function test_admin_can_access_create_page()
    {
        // Act
        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.accessibility-features.create'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.inclusive-radar.accessibility-features.create');
    }

    public function test_guest_cannot_access_edit()
    {
        // Arrange
        $feature = AccessibilityFeature::factory()->create();

        // Act
        $response = $this->get(route('inclusive-radar.accessibility-features.edit', $feature));

        // Assert
        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_edit()
    {
        // Arrange
        $feature = AccessibilityFeature::factory()->create();

        // Act
        $response = $this->actingAs($this->regularUser)
            ->get(route('inclusive-radar.accessibility-features.edit', $feature));

        // Assert
        $response->assertForbidden();
    }

    public function test_admin_can_access_edit_page()
    {
        // Arrange
        $feature = AccessibilityFeature::factory()->create();

        // Act
        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.accessibility-features.edit', $feature));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.inclusive-radar.accessibility-features.edit');
        $response->assertViewHas('accessibilityFeature', $feature);
    }

    public function test_guest_cannot_store_feature()
    {
        // Act
        $response = $this->post(
            route('inclusive-radar.accessibility-features.store'),
            ['name' => 'Teste']
        );

        // Assert
        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_store_feature()
    {
        // Act
        $response = $this->actingAs($this->regularUser)
            ->post(route('inclusive-radar.accessibility-features.store'), [
                'name' => 'Teste'
            ]);

        // Assert
        $response->assertForbidden();
    }

    public function test_admin_can_store_feature()
    {
        // Arrange
        $data = [
            'name' => 'Audiodescrição',
            'description' => 'Recurso importante',
            'is_active' => true,
        ];

        // Act
        $response = $this->actingAs($this->admin)
            ->post(route('inclusive-radar.accessibility-features.store'), $data);

        // Assert
        $response->assertRedirect(route('inclusive-radar.accessibility-features.index'));
        $this->assertDatabaseHas('accessibility_features', ['name' => 'Audiodescrição']);
    }

    public function test_store_requires_unique_name()
    {
        // Arrange
        AccessibilityFeature::factory()->create(['name' => 'Libras']);

        // Act
        $response = $this->actingAs($this->admin)
            ->post(route('inclusive-radar.accessibility-features.store'), [
                'name' => 'Libras'
            ]);

        // Assert
        $response->assertSessionHasErrors('name');
    }

    public function test_guest_cannot_view_feature()
    {
        // Arrange
        $feature = AccessibilityFeature::factory()->create();

        // Act
        $response = $this->get(route('inclusive-radar.accessibility-features.show', $feature));

        // Assert
        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_view_feature()
    {
        // Arrange
        $feature = AccessibilityFeature::factory()->create();

        // Act
        $response = $this->actingAs($this->regularUser)
            ->get(route('inclusive-radar.accessibility-features.show', $feature));

        // Assert
        $response->assertForbidden();
    }

    public function test_admin_can_view_feature()
    {
        // Arrange
        $feature = AccessibilityFeature::factory()->create();

        // Act
        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.accessibility-features.show', $feature));

        // Assert
        $response->assertOk();
        $response->assertViewHas('feature', $feature);
    }

    public function test_guest_cannot_update_feature()
    {
        // Arrange
        $feature = AccessibilityFeature::factory()->create();

        // Act
        $response = $this->put(
            route('inclusive-radar.accessibility-features.update', $feature),
            ['name' => 'Novo']
        );

        // Assert
        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_update_feature()
    {
        // Arrange
        $feature = AccessibilityFeature::factory()->create();

        // Act
        $response = $this->actingAs($this->regularUser)
            ->put(route('inclusive-radar.accessibility-features.update', $feature), [
                'name' => 'Novo'
            ]);

        // Assert
        $response->assertForbidden();
    }

    public function test_admin_can_update_feature()
    {
        // Arrange
        $feature = AccessibilityFeature::factory()->create();

        // Act
        $response = $this->actingAs($this->admin)
            ->put(route('inclusive-radar.accessibility-features.update', $feature), [
                'name' => 'Atualizado',
                'is_active' => true,
            ]);

        // Assert
        $response->assertRedirect(route('inclusive-radar.accessibility-features.index'));
        $this->assertDatabaseHas('accessibility_features', [
            'id' => $feature->id,
            'name' => 'Atualizado'
        ]);
    }

    public function test_guest_cannot_delete_feature()
    {
        // Arrange
        $feature = AccessibilityFeature::factory()->create();

        // Act
        $response = $this->delete(route('inclusive-radar.accessibility-features.destroy', $feature));

        // Assert
        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_delete_feature()
    {
        // Arrange
        $feature = AccessibilityFeature::factory()->create();

        // Act
        $response = $this->actingAs($this->regularUser)
            ->delete(route('inclusive-radar.accessibility-features.destroy', $feature));

        // Assert
        $response->assertForbidden();
    }

    public function test_admin_can_delete_feature()
    {
        // Arrange
        $feature = AccessibilityFeature::factory()->create();

        // Act
        $response = $this->actingAs($this->admin)
            ->delete(route('inclusive-radar.accessibility-features.destroy', $feature));

        // Assert
        $response->assertRedirect(route('inclusive-radar.accessibility-features.index'));
        $this->assertDatabaseMissing('accessibility_features', ['id' => $feature->id]);
    }

    public function test_index_can_filter_by_name_via_ajax()
    {
        // Arrange
        AccessibilityFeature::factory()->create(['name' => 'Braille']);
        AccessibilityFeature::factory()->create(['name' => 'Libras']);

        // Act
        $response = $this->actingAs($this->admin)
            ->get(
                route('inclusive-radar.accessibility-features.index', ['name' => 'Braille']),
                ['HTTP_X-Requested-With' => 'XMLHttpRequest']
            );

        // Assert
        $response->assertOk();
        $response->assertSee('Braille');
        $response->assertDontSee('Libras');
    }

    public function test_index_can_filter_by_status_via_ajax()
    {
        // Arrange
        AccessibilityFeature::factory()->active()->create(['name' => 'VISIBLE_ACTIVE']);
        AccessibilityFeature::factory()->inactive()->create(['name' => 'HIDDEN_INACTIVE']);

        // Act
        $response = $this->actingAs($this->admin)
            ->get(
                route('inclusive-radar.accessibility-features.index', ['is_active' => '1']),
                ['HTTP_X-Requested-With' => 'XMLHttpRequest']
            );

        // Assert
        $response->assertOk();
        $response->assertSee('VISIBLE_ACTIVE');
        $response->assertDontSee('HIDDEN_INACTIVE');
    }

    public function test_it_has_materials_relationship()
    {
        // Arrange
        $feature = new AccessibilityFeature();

        // Act
        $relation = $feature->materials();

        // Assert
        $this->assertInstanceOf(BelongsToMany::class, $relation);
        $this->assertEquals('accessible_educational_material_accessibility', $relation->getTable());
    }
}
