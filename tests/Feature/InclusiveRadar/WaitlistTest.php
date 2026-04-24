<?php

namespace Tests\Feature\InclusiveRadar;

use App\Enums\InclusiveRadar\WaitlistStatus;
use App\Models\InclusiveRadar\AssistiveTechnology;
use App\Models\InclusiveRadar\Waitlist;
use App\Models\User;
use App\Models\SpecializedEducationalSupport\Professional;
use App\Models\SpecializedEducationalSupport\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WaitlistTest extends TestCase
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

    public function test_guest_cannot_access_waitlists_index()
    {
        $response = $this->get(route('inclusive-radar.waitlists.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_waitlists_index()
    {
        $response = $this->actingAs($this->regularUser)
            ->get(route('inclusive-radar.waitlists.index'));

        $response->assertForbidden();
    }

    public function test_admin_can_list_waitlists()
    {
        Waitlist::factory()->count(2)->create();

        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.waitlists.index'));

        $response->assertOk();
        $response->assertViewIs('pages.inclusive-radar.waitlists.index');
        $response->assertViewHas('waitlists');
    }

    public function test_waitlists_index_returns_partial_when_ajax()
    {
        Waitlist::factory()->count(2)->create();

        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.waitlists.index'), [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $response->assertOk();
        $response->assertViewIs('pages.inclusive-radar.waitlists.partials.table');
        $response->assertViewHas('waitlists');
    }

    public function test_admin_can_access_waitlist_create_page()
    {
        Student::factory()->create();
        Professional::factory()->create();
        AssistiveTechnology::factory()->physical()->unavailable()->create([
            'quantity' => 1,
            'quantity_available' => 0,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.waitlists.create'));

        $response->assertOk();
        $response->assertViewIs('pages.inclusive-radar.waitlists.create');
        $response->assertViewHas('students');
        $response->assertViewHas('professionals');
        $response->assertViewHas('assistive_technologies');
        $response->assertViewHas('educational_materials');
    }

    public function test_admin_can_store_a_waitlist()
    {
        $student = Student::factory()->create();
        $item = AssistiveTechnology::factory()->physical()->unavailable()->create([
            'quantity' => 1,
            'quantity_available' => 0,
        ]);

        $data = [
            'waitlistable_id' => $item->id,
            'waitlistable_type' => 'assistive_technology',
            'student_id' => $student->id,
            'user_id' => $this->admin->id,
            'observation' => 'Entrou na fila',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('inclusive-radar.waitlists.store'), $data);

        $response->assertRedirect(route('inclusive-radar.waitlists.index'));
        $this->assertDatabaseHas('waitlists', [
            'waitlistable_id' => $item->id,
            'waitlistable_type' => $item->getMorphClass(),
            'student_id' => $student->id,
        ]);
    }

    public function test_admin_can_view_a_waitlist()
    {
        $waitlist = Waitlist::factory()->create([
            'status' => WaitlistStatus::WAITING->value,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.waitlists.show', $waitlist));

        $response->assertOk();
        $response->assertViewIs('pages.inclusive-radar.waitlists.show');
        $response->assertViewHas('waitlist');
        $response->assertViewHas('canCancel', true);
    }

    public function test_admin_can_access_waitlist_edit_page()
    {
        $waitlist = Waitlist::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.waitlists.edit', $waitlist));

        $response->assertOk();
        $response->assertViewIs('pages.inclusive-radar.waitlists.edit');
        $response->assertViewHas('waitlist');
        $response->assertViewHas('students');
        $response->assertViewHas('professionals');
    }

    public function test_admin_can_update_a_waitlist()
    {
        $waitlist = Waitlist::factory()->create([
            'status' => WaitlistStatus::WAITING->value,
        ]);

        $response = $this->actingAs($this->admin)
            ->put(route('inclusive-radar.waitlists.update', $waitlist), [
                'status' => WaitlistStatus::NOTIFIED->value,
                'observation' => 'Avisado',
            ]);

        $response->assertRedirect(route('inclusive-radar.waitlists.index'));
        $this->assertDatabaseHas('waitlists', [
            'id' => $waitlist->id,
            'status' => WaitlistStatus::NOTIFIED->value,
            'observation' => 'Avisado',
        ]);
    }

    public function test_admin_can_cancel_a_waitlist()
    {
        $waitlist = Waitlist::factory()->create([
            'status' => WaitlistStatus::WAITING->value,
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('inclusive-radar.waitlists.cancel', $waitlist));

        $response->assertRedirect();
        $this->assertDatabaseHas('waitlists', [
            'id' => $waitlist->id,
            'status' => WaitlistStatus::CANCELLED->value,
        ]);
    }

    public function test_admin_can_delete_a_waitlist()
    {
        $waitlist = Waitlist::factory()->create([
            'status' => WaitlistStatus::WAITING->value,
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('inclusive-radar.waitlists.destroy', $waitlist));

        $response->assertRedirect(route('inclusive-radar.waitlists.index'));
        $this->assertDatabaseMissing('waitlists', [
            'id' => $waitlist->id,
        ]);
    }

    public function test_admin_can_generate_waitlist_pdf()
    {
        $waitlist = Waitlist::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.waitlists.pdf', $waitlist));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }
}
