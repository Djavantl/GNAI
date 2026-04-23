<?php

namespace Tests\Feature\InclusiveRadar;

use App\Enums\InclusiveRadar\BarrierStatus;
use App\Enums\InclusiveRadar\InspectionType;
use App\Models\InclusiveRadar\Barrier;
use App\Models\InclusiveRadar\BarrierCategory;
use App\Models\InclusiveRadar\Institution;
use App\Models\InclusiveRadar\Inspection;
use App\Models\InclusiveRadar\Location;
use App\Models\SpecializedEducationalSupport\Deficiency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BarrierTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $regularUser;
    protected Institution $institution;
    protected BarrierCategory $category;
    protected Deficiency $deficiency;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->regularUser = User::factory()->create(['is_admin' => false]);
        $this->institution = Institution::factory()->create(['is_active' => true]);
        $this->category = BarrierCategory::factory()->create(['is_active' => true]);
        $this->deficiency = Deficiency::factory()->active()->create();
    }

    public function test_guest_cannot_access_barriers_index()
    {
        // Act
        $response = $this->get(route('inclusive-radar.barriers.index'));

        // Assert
        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_barriers_index()
    {
        // Act
        $response = $this->actingAs($this->regularUser)
            ->get(route('inclusive-radar.barriers.index'));

        // Assert
        $response->assertForbidden();
    }

    public function test_admin_can_list_barriers()
    {
        // Arrange
        Barrier::factory()->count(2)->create();

        // Act
        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.barriers.index'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.inclusive-radar.barriers.index');
        $response->assertViewHas('barriers');
    }

    public function test_barriers_index_returns_partial_when_ajax()
    {
        // Arrange
        Barrier::factory()->count(2)->create();

        // Act
        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.barriers.index'), [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.inclusive-radar.barriers.partials.table');
        $response->assertViewHas('barriers');
    }

    public function test_admin_can_access_barrier_create_page()
    {
        // Arrange
        Location::factory()->create(['institution_id' => $this->institution->id, 'is_active' => true]);

        // Act
        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.barriers.create'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.inclusive-radar.barriers.create');
        $response->assertViewHas('institutions');
        $response->assertViewHas('categories');
        $response->assertViewHas('deficiencies');
        $response->assertViewHas('defaultStatus', BarrierStatus::IDENTIFIED->value);
    }

    public function test_barrier_create_uses_old_selected_institution_when_available()
    {
        // Arrange
        $otherInstitution = Institution::factory()->create(['is_active' => true]);

        // Act
        $response = $this->actingAs($this->admin)
            ->withSession([
                '_old_input' => ['institution_id' => $otherInstitution->id],
            ])
            ->get(route('inclusive-radar.barriers.create'));

        // Assert
        $response->assertOk();
        $response->assertViewHas('selectedInstitution', function ($selectedInstitution) use ($otherInstitution) {
            return $selectedInstitution?->id === $otherInstitution->id;
        });
    }

    public function test_admin_can_store_an_anonymous_barrier()
    {
        // Arrange
        $location = Location::factory()->create(['institution_id' => $this->institution->id]);

        $data = [
            'name' => 'Piso escorregadio',
            'description' => 'Risco na entrada principal',
            'institution_id' => $this->institution->id,
            'barrier_category_id' => $this->category->id,
            'location_id' => $location->id,
            'priority' => 'high',
            'identified_at' => now()->toDateString(),
            'is_anonymous' => true,
            'deficiencies' => [$this->deficiency->id],
            'status' => BarrierStatus::IDENTIFIED->value,
            'inspection_type' => InspectionType::INITIAL->value,
            'inspection_date' => now()->toDateString(),
        ];

        // Act
        $response = $this->actingAs($this->admin)
            ->post(route('inclusive-radar.barriers.store'), $data);

        // Assert
        $response->assertRedirect(route('inclusive-radar.barriers.index'));
        $this->assertDatabaseHas('barriers', [
            'name' => 'Piso escorregadio',
            'is_anonymous' => true,
        ]);
    }

    public function test_it_fails_to_store_barrier_without_affected_person_when_not_anonymous_or_general()
    {
        // Arrange
        $data = [
            'name' => 'Barreira sem contexto',
            'institution_id' => $this->institution->id,
            'barrier_category_id' => $this->category->id,
            'priority' => 'medium',
            'identified_at' => now()->toDateString(),
            'deficiencies' => [$this->deficiency->id],
            'status' => BarrierStatus::IDENTIFIED->value,
            'inspection_type' => InspectionType::INITIAL->value,
            'inspection_date' => now()->toDateString(),
        ];

        // Act
        $response = $this->actingAs($this->admin)
            ->from(route('inclusive-radar.barriers.create'))
            ->post(route('inclusive-radar.barriers.store'), $data);

        // Assert
        $response->assertRedirect(route('inclusive-radar.barriers.create'));
        $response->assertSessionHasErrors(['affected_student_id', 'affected_professional_id']);
    }

    public function test_admin_can_update_a_barrier_to_resolved()
    {
        // Arrange
        $barrier = Barrier::factory()->anonymous()->create([
            'institution_id' => $this->institution->id,
            'barrier_category_id' => $this->category->id,
            'priority' => 'high',
        ]);

        $data = [
            'name' => $barrier->name,
            'description' => $barrier->description,
            'institution_id' => $barrier->institution_id,
            'barrier_category_id' => $barrier->barrier_category_id,
            'priority' => 'high',
            'identified_at' => $barrier->identified_at->toDateString(),
            'is_anonymous' => true,
            'deficiencies' => [$this->deficiency->id],
            'status' => BarrierStatus::RESOLVED->value,
            'inspection_type' => InspectionType::PERIODIC->value,
            'inspection_date' => now()->toDateString(),
            'inspection_description' => 'Correção executada',
        ];

        // Act
        $response = $this->actingAs($this->admin)
            ->put(route('inclusive-radar.barriers.update', $barrier), $data);

        // Assert
        $response->assertRedirect(route('inclusive-radar.barriers.index'));
        $this->assertNotNull($barrier->fresh()->resolved_at);
    }

    public function test_admin_can_view_a_barrier()
    {
        // Arrange
        $barrier = Barrier::factory()->create();

        // Act
        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.barriers.show', $barrier));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.inclusive-radar.barriers.show');
        $response->assertViewHas('barrier');
    }

    public function test_admin_can_access_barrier_edit_page()
    {
        // Arrange
        $barrier = Barrier::factory()->create([
            'institution_id' => $this->institution->id,
            'barrier_category_id' => $this->category->id,
        ]);

        // Act
        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.barriers.edit', $barrier));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.inclusive-radar.barriers.edit');
        $response->assertViewHas('barrier');
        $response->assertViewHas('institutions');
        $response->assertViewHas('categories');
    }

    public function test_barrier_edit_uses_old_selected_institution_when_available()
    {
        // Arrange
        $otherInstitution = Institution::factory()->create(['is_active' => true]);
        $barrier = Barrier::factory()->create([
            'institution_id' => $this->institution->id,
            'barrier_category_id' => $this->category->id,
        ]);

        // Act
        $response = $this->actingAs($this->admin)
            ->withSession([
                '_old_input' => ['institution_id' => $otherInstitution->id],
            ])
            ->get(route('inclusive-radar.barriers.edit', $barrier));

        // Assert
        $response->assertOk();
        $response->assertViewHas('selectedInstitution', function ($selectedInstitution) use ($otherInstitution) {
            return $selectedInstitution?->id === $otherInstitution->id;
        });
    }

    public function test_admin_can_delete_a_barrier()
    {
        // Arrange
        $barrier = Barrier::factory()->create();

        // Act
        $response = $this->actingAs($this->admin)
            ->delete(route('inclusive-radar.barriers.destroy', $barrier));

        // Assert
        $response->assertRedirect(route('inclusive-radar.barriers.index'));
        $this->assertDatabaseMissing('barriers', [
            'id' => $barrier->id,
        ]);
    }

    public function test_admin_can_generate_barrier_pdf()
    {
        // Arrange
        $barrier = Barrier::factory()->create(['name' => 'Barreira PDF']);

        // Act
        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.barriers.pdf', $barrier));

        // Assert
        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_can_view_a_barrier_inspection()
    {
        // Arrange
        $barrier = Barrier::factory()->create();
        $inspection = Inspection::factory()->forBarrier($barrier)->create();

        // Act
        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.barriers.inspection.show', [$barrier, $inspection]));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.inclusive-radar.barriers.inspections.show');
        $response->assertViewHas('inspection', $inspection);
    }

    public function test_it_blocks_access_to_an_inspection_of_another_barrier()
    {
        // Arrange
        $barrier = Barrier::factory()->create();
        $otherBarrier = Barrier::factory()->create();
        $inspection = Inspection::factory()->forBarrier($otherBarrier)->create();

        // Act
        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.barriers.inspection.show', [$barrier, $inspection]));

        // Assert
        $response->assertForbidden();
    }
}
