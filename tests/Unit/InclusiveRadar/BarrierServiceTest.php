<?php

namespace Tests\Unit\InclusiveRadar;

use App\Enums\InclusiveRadar\BarrierStatus;
use App\Enums\InclusiveRadar\InspectionType;
use App\Models\InclusiveRadar\Barrier;
use App\Models\InclusiveRadar\BarrierCategory;
use App\Domains\InclusiveRadar\Domain\Models\Institution;
use App\Models\InclusiveRadar\Inspection;
use App\Models\InclusiveRadar\Location;
use App\Models\SpecializedEducationalSupport\Professional;
use App\Models\SpecializedEducationalSupport\Deficiency;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\User;
use App\Services\InclusiveRadar\BarrierService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BarrierServiceTest extends TestCase
{
    use RefreshDatabase;

    protected BarrierService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(BarrierService::class);
    }

    public function test_it_stores_a_barrier_and_creates_the_initial_inspection()
    {
        // Arrange
        $user = User::factory()->create(['is_admin' => true]);
        $institution = Institution::factory()->create();
        $location = Location::factory()->create(['institution_id' => $institution->id]);
        $category = BarrierCategory::factory()->create();
        $student = Student::factory()->create();
        $deficiency = Deficiency::factory()->create();

        $this->actingAs($user);

        $data = [
            'name' => 'Escada sem corrimão',
            'description' => 'Acesso principal comprometido',
            'institution_id' => $institution->id,
            'barrier_category_id' => $category->id,
            'location_id' => $location->id,
            'priority' => 'high',
            'identified_at' => now()->toDateString(),
            'affected_student_id' => $student->id,
            'deficiencies' => [$deficiency->id],
            'status' => BarrierStatus::IDENTIFIED->value,
            'inspection_type' => InspectionType::INITIAL->value,
            'inspection_date' => now()->toDateString(),
            'inspection_description' => 'Registro inicial',
        ];

        // Act
        $barrier = $this->service->store($data);

        // Assert
        $this->assertInstanceOf(Barrier::class, $barrier);
        $this->assertSame($user->id, $barrier->registered_by_user_id);
        $this->assertCount(1, $barrier->deficiencies);
        $this->assertDatabaseHas('inspections', [
            'inspectable_id' => $barrier->id,
            'inspectable_type' => $barrier->getMorphClass(),
            'status' => BarrierStatus::IDENTIFIED->value,
            'type' => InspectionType::INITIAL->value,
        ]);
    }

    public function test_it_sanitizes_reporter_data_for_anonymous_barriers()
    {
        // Arrange
        $user = User::factory()->create(['is_admin' => true]);
        $institution = Institution::factory()->create();
        $category = BarrierCategory::factory()->create();
        $deficiency = Deficiency::factory()->create();
        $student = Student::factory()->create();

        $this->actingAs($user);

        $data = [
            'name' => 'Relato anônimo',
            'institution_id' => $institution->id,
            'barrier_category_id' => $category->id,
            'priority' => 'medium',
            'identified_at' => now()->toDateString(),
            'affected_student_id' => $student->id,
            'deficiencies' => [$deficiency->id],
            'is_anonymous' => true,
            'status' => BarrierStatus::IDENTIFIED->value,
            'inspection_type' => InspectionType::INITIAL->value,
            'inspection_date' => now()->toDateString(),
        ];

        // Act
        $barrier = $this->service->store($data);

        // Assert
        $this->assertTrue($barrier->is_anonymous);
        $this->assertNull($barrier->affected_student_id);
        $this->assertNull($barrier->affected_professional_id);
        $this->assertNull($barrier->affected_person_name);
        $this->assertNull($barrier->affected_person_role);
    }

    public function test_it_updates_a_barrier_to_resolved_and_registers_a_new_inspection()
    {
        // Arrange
        $user = User::factory()->create(['is_admin' => true]);
        $deficiency = Deficiency::factory()->create();
        $barrier = Barrier::factory()->create([
            'resolved_at' => null,
        ]);

        $barrier->deficiencies()->sync([$deficiency->id]);

        $this->actingAs($user);

        $data = [
            'name' => $barrier->name,
            'description' => $barrier->description,
            'institution_id' => $barrier->institution_id,
            'barrier_category_id' => $barrier->barrier_category_id,
            'location_id' => $barrier->location_id,
            'priority' => $barrier->priority->value,
            'identified_at' => $barrier->identified_at->toDateString(),
            'is_anonymous' => true,
            'deficiencies' => [$deficiency->id],
            'status' => BarrierStatus::RESOLVED->value,
            'inspection_type' => InspectionType::PERIODIC->value,
            'inspection_date' => now()->toDateString(),
            'inspection_description' => 'Barreira resolvida',
        ];

        // Act
        $updated = $this->service->update($barrier, $data);

        // Assert
        $this->assertNotNull($updated->resolved_at);
        $this->assertDatabaseHas('inspections', [
            'inspectable_id' => $barrier->id,
            'inspectable_type' => $barrier->getMorphClass(),
            'status' => BarrierStatus::RESOLVED->value,
            'type' => InspectionType::PERIODIC->value,
        ]);
    }

    public function test_it_creates_a_follow_up_inspection_when_updating_an_existing_barrier()
    {
        // Arrange
        $user = User::factory()->create(['is_admin' => true]);
        $deficiency = Deficiency::factory()->create();
        $barrier = Barrier::factory()->anonymous()->create();
        $barrier->deficiencies()->sync([$deficiency->id]);
        Inspection::factory()->forBarrier($barrier)->create([
            'status' => BarrierStatus::IDENTIFIED->value,
        ]);

        $this->actingAs($user);

        $initialCount = $barrier->inspections()->count();

        $data = [
            'name' => $barrier->name,
            'description' => $barrier->description,
            'institution_id' => $barrier->institution_id,
            'barrier_category_id' => $barrier->barrier_category_id,
            'location_id' => $barrier->location_id,
            'priority' => $barrier->priority->value,
            'identified_at' => $barrier->identified_at->toDateString(),
            'is_anonymous' => true,
            'deficiencies' => [$deficiency->id],
        ];

        // Act
        $this->service->update($barrier, $data);

        // Assert
        $this->assertSame($initialCount + 1, $barrier->fresh()->inspections()->count());
    }

    public function test_it_deletes_a_barrier()
    {
        // Arrange
        $barrier = Barrier::factory()->create();

        // Act
        $this->service->delete($barrier);

        // Assert
        $this->assertDatabaseMissing('barriers', [
            'id' => $barrier->id,
        ]);
    }

    public function test_it_clears_location_when_no_location_flag_is_informed()
    {
        // Arrange
        $user = User::factory()->create(['is_admin' => true]);
        $institution = Institution::factory()->create();
        $location = Location::factory()->create(['institution_id' => $institution->id]);
        $category = BarrierCategory::factory()->create();
        $student = Student::factory()->create();
        $deficiency = Deficiency::factory()->create();

        $this->actingAs($user);

        $data = [
            'name' => 'Barreira sem local marcado',
            'institution_id' => $institution->id,
            'barrier_category_id' => $category->id,
            'location_id' => $location->id,
            'no_location' => true,
            'priority' => 'high',
            'identified_at' => now()->toDateString(),
            'affected_student_id' => $student->id,
            'deficiencies' => [$deficiency->id],
            'status' => BarrierStatus::IDENTIFIED->value,
            'inspection_type' => InspectionType::INITIAL->value,
            'inspection_date' => now()->toDateString(),
        ];

        // Act
        $barrier = $this->service->store($data);

        // Assert
        $this->assertNull($barrier->location_id);
        $this->assertSame($user->id, $barrier->registered_by_user_id);
    }

    public function test_it_preserves_registered_by_user_on_update()
    {
        // Arrange
        $originalUser = User::factory()->create(['is_admin' => true]);
        $updatingUser = User::factory()->create(['is_admin' => true]);
        $deficiency = Deficiency::factory()->create();
        $barrier = Barrier::factory()->anonymous()->create([
            'registered_by_user_id' => $originalUser->id,
            'priority' => 'high',
        ]);

        $barrier->deficiencies()->sync([$deficiency->id]);

        $this->actingAs($updatingUser);

        $data = [
            'name' => $barrier->name,
            'description' => $barrier->description,
            'institution_id' => $barrier->institution_id,
            'barrier_category_id' => $barrier->barrier_category_id,
            'location_id' => $barrier->location_id,
            'priority' => 'high',
            'identified_at' => $barrier->identified_at->toDateString(),
            'is_anonymous' => true,
            'deficiencies' => [$deficiency->id],
            'inspection_description' => 'Atualizacao sem alterar autor',
        ];

        // Act
        $updated = $this->service->update($barrier, $data);

        // Assert
        $this->assertSame($originalUser->id, $updated->registered_by_user_id);
    }

    public function test_it_sanitizes_reporter_data_for_general_reports()
    {
        // Arrange
        $user = User::factory()->create(['is_admin' => true]);
        $institution = Institution::factory()->create();
        $category = BarrierCategory::factory()->create();
        $student = Student::factory()->create();
        $professional = Professional::factory()->create();
        $deficiency = Deficiency::factory()->create();

        $this->actingAs($user);

        $data = [
            'name' => 'Relato geral',
            'institution_id' => $institution->id,
            'barrier_category_id' => $category->id,
            'priority' => 'medium',
            'identified_at' => now()->toDateString(),
            'affected_student_id' => $student->id,
            'affected_professional_id' => $professional->id,
            'affected_person_name' => 'Visitante impactado',
            'affected_person_role' => 'Visitante',
            'not_applicable' => true,
            'deficiencies' => [$deficiency->id],
            'status' => BarrierStatus::IDENTIFIED->value,
            'inspection_type' => InspectionType::INITIAL->value,
            'inspection_date' => now()->toDateString(),
        ];

        // Act
        $barrier = $this->service->store($data);

        // Assert
        $this->assertTrue($barrier->not_applicable);
        $this->assertFalse($barrier->is_anonymous);
        $this->assertNull($barrier->affected_student_id);
        $this->assertNull($barrier->affected_professional_id);
        $this->assertSame('Visitante impactado', $barrier->affected_person_name);
        $this->assertSame('Visitante', $barrier->affected_person_role);
    }

    public function test_it_sanitizes_reporter_data_for_identifiable_internal_reports()
    {
        // Arrange
        $user = User::factory()->create(['is_admin' => true]);
        $institution = Institution::factory()->create();
        $category = BarrierCategory::factory()->create();
        $student = Student::factory()->create();
        $professional = Professional::factory()->create();
        $deficiency = Deficiency::factory()->create();

        $this->actingAs($user);

        $data = [
            'name' => 'Relato identificado interno',
            'institution_id' => $institution->id,
            'barrier_category_id' => $category->id,
            'priority' => 'medium',
            'identified_at' => now()->toDateString(),
            'affected_student_id' => $student->id,
            'affected_professional_id' => $professional->id,
            'affected_person_name' => 'Texto deve ser limpo',
            'affected_person_role' => 'Texto deve ser limpo',
            'deficiencies' => [$deficiency->id],
            'status' => BarrierStatus::IDENTIFIED->value,
            'inspection_type' => InspectionType::INITIAL->value,
            'inspection_date' => now()->toDateString(),
        ];

        // Act
        $barrier = $this->service->store($data);

        // Assert
        $this->assertFalse($barrier->is_anonymous);
        $this->assertFalse($barrier->not_applicable);
        $this->assertSame($student->id, $barrier->affected_student_id);
        $this->assertSame($professional->id, $barrier->affected_professional_id);
        $this->assertNull($barrier->affected_person_name);
        $this->assertNull($barrier->affected_person_role);
    }

    public function test_it_sets_resolved_at_when_status_is_not_applicable()
    {
        // Arrange
        $user = User::factory()->create(['is_admin' => true]);
        $deficiency = Deficiency::factory()->create();
        $barrier = Barrier::factory()->anonymous()->create([
            'resolved_at' => null,
            'priority' => 'high',
        ]);

        $barrier->deficiencies()->sync([$deficiency->id]);

        $this->actingAs($user);

        $data = [
            'name' => $barrier->name,
            'description' => $barrier->description,
            'institution_id' => $barrier->institution_id,
            'barrier_category_id' => $barrier->barrier_category_id,
            'location_id' => $barrier->location_id,
            'priority' => 'high',
            'identified_at' => $barrier->identified_at->toDateString(),
            'is_anonymous' => true,
            'deficiencies' => [$deficiency->id],
            'status' => BarrierStatus::NOT_APPLICABLE->value,
            'inspection_type' => InspectionType::PERIODIC->value,
            'inspection_date' => now()->toDateString(),
            'inspection_description' => 'Classificada como nao aplicavel',
        ];

        // Act
        $updated = $this->service->update($barrier, $data);

        // Assert
        $this->assertNotNull($updated->resolved_at);
        $this->assertDatabaseHas('inspections', [
            'inspectable_id' => $barrier->id,
            'inspectable_type' => $barrier->getMorphClass(),
            'status' => BarrierStatus::NOT_APPLICABLE->value,
        ]);
    }

    public function test_it_does_not_create_a_new_inspection_when_update_has_no_status_change_or_interaction()
    {
        // Arrange
        $user = User::factory()->create(['is_admin' => true]);
        $deficiency = Deficiency::factory()->create();
        $barrier = Barrier::factory()->anonymous()->create([
            'priority' => 'high',
        ]);

        $barrier->deficiencies()->sync([$deficiency->id]);
        Inspection::factory()->forBarrier($barrier)->create([
            'status' => BarrierStatus::IDENTIFIED->value,
            'type' => InspectionType::INITIAL->value,
        ]);
        $barrier = $barrier->fresh();

        $this->actingAs($user);

        $initialCount = $barrier->inspections()->count();

        $data = [
            'name' => $barrier->name,
            'description' => $barrier->description,
            'institution_id' => $barrier->institution_id,
            'barrier_category_id' => $barrier->barrier_category_id,
            'location_id' => $barrier->location_id,
            'priority' => 'high',
            'identified_at' => $barrier->identified_at->toDateString(),
            'is_anonymous' => true,
            'deficiencies' => [$deficiency->id],
        ];

        // Act
        $updated = $this->service->update($barrier, $data);

        // Assert
        $this->assertSame($initialCount, $updated->inspections()->count());
    }
}
