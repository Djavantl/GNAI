<?php

namespace Tests\Feature\InclusiveRadar;

use App\Enums\InclusiveRadar\ConservationState;
use App\Enums\InclusiveRadar\InspectionType;
use App\Enums\InclusiveRadar\ResourceStatus;
use App\Models\AuditLog;
use App\Models\InclusiveRadar\AccessibleEducationalMaterial;
use App\Models\InclusiveRadar\AccessibilityFeature;
use App\Models\InclusiveRadar\Inspection;
use App\Models\SpecializedEducationalSupport\Deficiency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessibleEducationalMaterialTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->regularUser = User::factory()->create(['is_admin' => false]);
    }

    public function test_guest_cannot_access_materials_index()
    {
        // Act
        $response = $this->get(route('inclusive-radar.accessible-educational-materials.index'));

        // Assert
        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_materials_index()
    {
        // Act
        $response = $this->actingAs($this->regularUser)
            ->get(route('inclusive-radar.accessible-educational-materials.index'));

        // Assert
        $response->assertForbidden();
    }

    public function test_admin_can_list_materials()
    {
        // Arrange
        AccessibleEducationalMaterial::factory()->count(2)->create();

        // Act
        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.accessible-educational-materials.index'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.inclusive-radar.accessible-educational-materials.index');
        $response->assertViewHas('materials');
    }

    public function test_materials_index_returns_partial_when_ajax()
    {
        // Arrange
        AccessibleEducationalMaterial::factory()->count(2)->create();

        // Act
        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.accessible-educational-materials.index'), [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.inclusive-radar.accessible-educational-materials.partials.table');
        $response->assertViewHas('materials');
    }

    public function test_admin_can_access_material_create_page()
    {
        // Arrange
        Deficiency::factory()->count(2)->create();
        AccessibilityFeature::factory()->count(2)->create(['is_active' => true]);

        // Act
        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.accessible-educational-materials.create'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.inclusive-radar.accessible-educational-materials.create');
        $response->assertViewHas('deficiencies');
        $response->assertViewHas('accessibilityFeatures');
        $response->assertViewHas('defaultInspection', InspectionType::INITIAL->value);
    }

    public function test_admin_can_store_a_material_with_valid_data()
    {
        // Arrange
        $deficiency = Deficiency::factory()->create();
        $feature = AccessibilityFeature::factory()->create();

        $data = [
            'name' => 'MPA de Matemática',
            'is_digital' => false,
            'is_loanable' => true,
            'asset_code' => 'PAT-5001',
            'quantity' => 4,
            'status' => ResourceStatus::AVAILABLE->value,
            'deficiencies' => [$deficiency->id],
            'accessibility_features' => [$feature->id],
            'conservation_state' => ConservationState::GOOD->value,
            'inspection_type' => InspectionType::INITIAL->value,
            'inspection_date' => now()->toDateString(),
        ];

        // Act
        $response = $this->actingAs($this->admin)
            ->post(route('inclusive-radar.accessible-educational-materials.store'), $data);

        // Assert
        $response->assertRedirect(route('inclusive-radar.accessible-educational-materials.index'));
        $this->assertDatabaseHas('accessible_educational_materials', [
            'name' => 'MPA de Matemática',
            'asset_code' => 'PAT-5001',
        ]);
    }

    public function test_it_fails_to_store_material_without_deficiencies()
    {
        // Arrange
        $data = [
            'name' => 'MPA sem Público',
            'is_digital' => false,
            'quantity' => 2,
            'status' => ResourceStatus::AVAILABLE->value,
            'deficiencies' => [],
            'conservation_state' => ConservationState::GOOD->value,
            'inspection_type' => InspectionType::INITIAL->value,
            'inspection_date' => now()->toDateString(),
        ];

        // Act
        $response = $this->actingAs($this->admin)
            ->from(route('inclusive-radar.accessible-educational-materials.create'))
            ->post(route('inclusive-radar.accessible-educational-materials.store'), $data);

        // Assert
        $response->assertRedirect(route('inclusive-radar.accessible-educational-materials.create'));
        $response->assertSessionHasErrors('deficiencies');
    }

    public function test_admin_can_update_a_material()
    {
        // Arrange
        $deficiency = Deficiency::factory()->create();
        $feature = AccessibilityFeature::factory()->create();
        $material = AccessibleEducationalMaterial::factory()->physical()->available()->create([
            'quantity' => 3,
            'quantity_available' => 3,
        ]);

        $data = [
            'name' => 'MPA Atualizado',
            'is_digital' => false,
            'is_loanable' => true,
            'asset_code' => $material->asset_code,
            'quantity' => 7,
            'quantity_available' => 7,
            'status' => ResourceStatus::AVAILABLE->value,
            'deficiencies' => [$deficiency->id],
            'accessibility_features' => [$feature->id],
            'conservation_state' => ConservationState::REGULAR->value,
            'inspection_date' => now()->toDateString(),
        ];

        // Act
        $response = $this->actingAs($this->admin)
            ->put(route('inclusive-radar.accessible-educational-materials.update', $material), $data);

        // Assert
        $response->assertRedirect(route('inclusive-radar.accessible-educational-materials.index'));
        $this->assertDatabaseHas('accessible_educational_materials', [
            'id' => $material->id,
            'name' => 'MPA Atualizado',
            'quantity' => 7,
        ]);
    }

    public function test_admin_can_view_a_material()
    {
        // Arrange
        $material = AccessibleEducationalMaterial::factory()->create();

        // Act
        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.accessible-educational-materials.show', $material));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.inclusive-radar.accessible-educational-materials.show');
        $response->assertViewHas('material');
        $response->assertViewHas('deficiencies');
        $response->assertViewHas('features');
        $response->assertViewHas('inspections');
    }

    public function test_admin_can_access_material_edit_page()
    {
        // Arrange
        Deficiency::factory()->count(2)->create();
        AccessibilityFeature::factory()->count(2)->create(['is_active' => true]);
        $material = AccessibleEducationalMaterial::factory()->create();

        // Act
        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.accessible-educational-materials.edit', $material));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.inclusive-radar.accessible-educational-materials.edit');
        $response->assertViewHas('material');
        $response->assertViewHas('activeLoans');
        $response->assertViewHas('defaultInspection', InspectionType::PERIODIC->value);
    }

    public function test_admin_can_delete_a_material()
    {
        // Arrange
        $material = AccessibleEducationalMaterial::factory()->create();

        // Act
        $response = $this->actingAs($this->admin)
            ->delete(route('inclusive-radar.accessible-educational-materials.destroy', $material));

        // Assert
        $response->assertRedirect(route('inclusive-radar.accessible-educational-materials.index'));
        $this->assertSoftDeleted('accessible_educational_materials', [
            'id' => $material->id,
        ]);
    }

    public function test_admin_can_generate_material_pdf()
    {
        // Arrange
        $material = AccessibleEducationalMaterial::factory()->create(['name' => 'Material PDF']);

        // Act
        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.accessible-educational-materials.pdf', $material));

        // Assert
        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_can_view_a_material_inspection()
    {
        // Arrange
        $material = AccessibleEducationalMaterial::factory()->create();
        $inspection = Inspection::factory()
            ->forAccessibleEducationalMaterial($material)
            ->create();

        // Act
        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.accessible-educational-materials.inspection.show', [$material, $inspection]));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.inclusive-radar.accessible-educational-materials.inspections.show');
        $response->assertViewHas('inspection', $inspection);
    }

    public function test_it_blocks_access_to_an_inspection_of_another_material()
    {
        // Arrange
        $material = AccessibleEducationalMaterial::factory()->create();
        $otherMaterial = AccessibleEducationalMaterial::factory()->create();
        $inspection = Inspection::factory()
            ->forAccessibleEducationalMaterial($otherMaterial)
            ->create();

        // Act
        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.accessible-educational-materials.inspection.show', [$material, $inspection]));

        // Assert
        $response->assertForbidden();
    }

    public function test_guest_cannot_access_material_logs()
    {
        $material = AccessibleEducationalMaterial::factory()->create();

        $response = $this->get(route('inclusive-radar.accessible-educational-materials.logs', $material));

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_material_logs()
    {
        $material = AccessibleEducationalMaterial::factory()->create();

        $response = $this->actingAs($this->regularUser)
            ->get(route('inclusive-radar.accessible-educational-materials.logs', $material));

        $response->assertForbidden();
    }

    public function test_admin_can_view_material_logs()
    {
        $material = AccessibleEducationalMaterial::factory()->create();

        AuditLog::create([
            'user_id' => $this->admin->id,
            'action' => 'updated',
            'auditable_type' => $material->getMorphClass(),
            'auditable_id' => $material->id,
            'old_values' => ['name' => 'Anterior'],
            'new_values' => ['name' => 'Atual'],
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.accessible-educational-materials.logs', $material));

        $response->assertOk();
        $response->assertViewIs('pages.inclusive-radar.accessible-educational-materials.logs.logs');
        $response->assertViewHas('material', $material);
        $response->assertViewHas('logs');
    }
}
