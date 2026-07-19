<?php

namespace Tests\Feature\InclusiveRadar;

use App\Enums\InclusiveRadar\ConservationState;
use App\Enums\InclusiveRadar\InspectionType;
use App\Enums\InclusiveRadar\ResourceStatus;
use App\Models\AuditLog;
use App\Models\InclusiveRadar\AssistiveTechnology;
use App\Models\InclusiveRadar\Inspection;
use App\Models\SpecializedEducationalSupport\Deficiency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssistiveTechnologyTest extends TestCase
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

    public function test_guest_cannot_access_assistive_technologies_index()
    {
        // Act
        $response = $this->get(route('inclusive-radar.assistive-technologies.index'));

        // Assert
        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_assistive_technologies_index()
    {
        // Act
        $response = $this->actingAs($this->regularUser)
            ->get(route('inclusive-radar.assistive-technologies.index'));

        // Assert
        $response->assertForbidden();
    }

    public function test_admin_can_list_assistive_technologies()
    {
        // Arrange
        AssistiveTechnology::factory()->count(2)->create();

        // Act
        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.assistive-technologies.index'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.inclusive-radar.assistive-technologies.index');
        $response->assertViewHas('assistiveTechnologies');
    }

    public function test_assistive_technologies_index_returns_partial_when_ajax()
    {
        // Arrange
        AssistiveTechnology::factory()->count(2)->create();

        // Act
        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.assistive-technologies.index'), [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.inclusive-radar.assistive-technologies.partials.table');
        $response->assertViewHas('assistiveTechnologies');
    }

    public function test_admin_can_access_assistive_technology_create_page()
    {
        // Arrange
        Deficiency::factory()->count(2)->create();

        // Act
        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.assistive-technologies.create'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.inclusive-radar.assistive-technologies.create');
        $response->assertViewHas('deficiencies');
        $response->assertViewHas('defaultInspection', InspectionType::INITIAL->value);
    }

    public function test_admin_can_store_an_assistive_technology_with_valid_data()
    {
        // Arrange
        $deficiency = Deficiency::factory()->create();

        $data = [
            'name' => 'Software Leitor de Tela',
            'is_digital' => true,
            'is_loanable' => false,
            'asset_code' => 'TA-7001',
            'status' => ResourceStatus::AVAILABLE->value,
            'is_active' => true,
            'deficiencies' => [$deficiency->id],
            'conservation_state' => ConservationState::NOT_APPLICABLE->value,
            'inspection' => [
                'type' => InspectionType::INITIAL->value,
                'date' => now()->toDateString(),
            ],
        ];

        // Act
        $response = $this->actingAs($this->admin)
            ->post(route('inclusive-radar.assistive-technologies.store'), $data);

        // Assert
        $response->assertRedirect(route('inclusive-radar.assistive-technologies.index'));
        $this->assertDatabaseHas('assistive_technologies', [
            'name' => 'Software Leitor de Tela',
            'asset_code' => 'TA-7001',
        ]);
    }

    public function test_it_fails_to_store_assistive_technology_without_name()
    {
        // Arrange
        $deficiency = Deficiency::factory()->create();

        $data = [
            'name' => '',
            'is_digital' => false,
            'quantity' => 1,
            'status' => ResourceStatus::AVAILABLE->value,
            'is_active' => true,
            'deficiencies' => [$deficiency->id],
            'conservation_state' => ConservationState::GOOD->value,
            'inspection' => [
                'type' => InspectionType::INITIAL->value,
                'date' => now()->toDateString(),
            ],
        ];

        // Act
        $response = $this->actingAs($this->admin)
            ->from(route('inclusive-radar.assistive-technologies.create'))
            ->post(route('inclusive-radar.assistive-technologies.store'), $data);

        // Assert
        $response->assertRedirect(route('inclusive-radar.assistive-technologies.create'));
        $response->assertSessionHasErrors('name');
    }

    public function test_admin_can_update_an_assistive_technology()
    {
        // Arrange
        $deficiency = Deficiency::factory()->create();
        $technology = AssistiveTechnology::factory()->physical()->available()->create([
            'quantity' => 2,
            'quantity_available' => 2,
        ]);

        $data = [
            'name' => 'TA Atualizada',
            'is_digital' => false,
            'is_loanable' => true,
            'asset_code' => $technology->asset_code,
            'quantity' => 5,
            'status' => ResourceStatus::AVAILABLE->value,
            'is_active' => true,
            'deficiencies' => [$deficiency->id],
            'conservation_state' => ConservationState::GOOD->value,
            'inspection' => [
                'type' => InspectionType::PERIODIC->value,
                'date' => now()->toDateString(),
            ],
        ];

        // Act
        $response = $this->actingAs($this->admin)
            ->put(route('inclusive-radar.assistive-technologies.update', $technology), $data);

        // Assert
        $response->assertRedirect(route('inclusive-radar.assistive-technologies.index'));
        $this->assertDatabaseHas('assistive_technologies', [
            'id' => $technology->id,
            'name' => 'TA Atualizada',
            'quantity' => 5,
        ]);
    }

    public function test_admin_can_view_an_assistive_technology()
    {
        // Arrange
        $technology = AssistiveTechnology::factory()->create();

        // Act
        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.assistive-technologies.show', $technology));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.inclusive-radar.assistive-technologies.show');
        $response->assertViewHas('assistiveTechnology');
        $response->assertViewHas('deficiencies');
        $response->assertViewHas('inspections');
    }

    public function test_admin_can_access_assistive_technology_edit_page()
    {
        // Arrange
        Deficiency::factory()->count(2)->create();
        $technology = AssistiveTechnology::factory()->create();

        // Act
        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.assistive-technologies.edit', $technology));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.inclusive-radar.assistive-technologies.edit');
        $response->assertViewHas('assistiveTechnology');
        $response->assertViewHas('activeLoans');
        $response->assertViewHas('defaultInspection', InspectionType::PERIODIC->value);
    }

    public function test_admin_can_delete_an_assistive_technology()
    {
        // Arrange
        $technology = AssistiveTechnology::factory()->create();

        // Act
        $response = $this->actingAs($this->admin)
            ->delete(route('inclusive-radar.assistive-technologies.destroy', $technology));

        // Assert
        $response->assertRedirect(route('inclusive-radar.assistive-technologies.index'));
        $this->assertSoftDeleted('assistive_technologies', [
            'id' => $technology->id,
        ]);
    }

    public function test_admin_can_generate_assistive_technology_pdf()
    {
        // Arrange
        $technology = AssistiveTechnology::factory()->create(['name' => 'TA PDF']);

        // Act
        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.assistive-technologies.pdf', $technology));

        // Assert
        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_can_view_an_assistive_technology_inspection()
    {
        // Arrange
        $technology = AssistiveTechnology::factory()->create();
        $inspection = Inspection::factory()
            ->forAssistiveTechnology($technology)
            ->create();

        // Act
        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.assistive-technologies.inspection.show', [$technology, $inspection]));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.inclusive-radar.assistive-technologies.inspections.show');
        $response->assertViewHas(
            'inspection',
            static fn ($loadedInspection): bool => $loadedInspection->is($inspection),
        );
    }

    public function test_it_blocks_access_to_an_inspection_of_another_assistive_technology()
    {
        // Arrange
        $technology = AssistiveTechnology::factory()->create();
        $otherTechnology = AssistiveTechnology::factory()->create();
        $inspection = Inspection::factory()
            ->forAssistiveTechnology($otherTechnology)
            ->create();

        // Act
        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.assistive-technologies.inspection.show', [$technology, $inspection]));

        // Assert
        $response->assertForbidden();
    }

    public function test_guest_cannot_access_assistive_technology_logs()
    {
        $technology = AssistiveTechnology::factory()->create();

        $response = $this->get(route('inclusive-radar.assistive-technologies.logs', $technology));

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_assistive_technology_logs()
    {
        $technology = AssistiveTechnology::factory()->create();

        $response = $this->actingAs($this->regularUser)
            ->get(route('inclusive-radar.assistive-technologies.logs', $technology));

        $response->assertForbidden();
    }

    public function test_admin_can_view_assistive_technology_logs()
    {
        $technology = AssistiveTechnology::factory()->create();

        AuditLog::create([
            'user_id' => $this->admin->id,
            'action' => 'updated',
            'auditable_type' => $technology->getMorphClass(),
            'auditable_id' => $technology->id,
            'old_values' => ['name' => 'Anterior'],
            'new_values' => ['name' => 'Atual'],
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.assistive-technologies.logs', $technology));

        $response->assertOk();
        $response->assertViewIs('pages.inclusive-radar.assistive-technologies.logs.logs');
        $response->assertViewHas('assistiveTechnology', $technology);
        $response->assertViewHas('logs');
    }
}
