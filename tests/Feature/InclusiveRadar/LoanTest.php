<?php

namespace Tests\Feature\InclusiveRadar;

use App\Enums\InclusiveRadar\LoanStatus;
use App\Models\InclusiveRadar\AssistiveTechnology;
use App\Models\InclusiveRadar\Loan;
use App\Models\SpecializedEducationalSupport\Professional;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanTest extends TestCase
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

    public function test_guest_cannot_access_loans_index()
    {
        $response = $this->get(route('inclusive-radar.loans.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_loans_index()
    {
        $response = $this->actingAs($this->regularUser)
            ->get(route('inclusive-radar.loans.index'));

        $response->assertForbidden();
    }

    public function test_admin_can_list_loans()
    {
        Loan::factory()->count(2)->create();

        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.loans.index'));

        $response->assertOk();
        $response->assertViewIs('pages.inclusive-radar.loans.index');
        $response->assertViewHas('loans');
    }

    public function test_loans_index_returns_partial_when_ajax()
    {
        Loan::factory()->count(2)->create();

        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.loans.index'), [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $response->assertOk();
        $response->assertViewIs('pages.inclusive-radar.loans.partials.table');
        $response->assertViewHas('loans');
    }

    public function test_admin_can_access_loan_create_page()
    {
        $student = Student::factory()->create();
        $professional = Professional::factory()->create();
        $item = AssistiveTechnology::factory()->physical()->available()->loanable()->create([
            'quantity' => 2,
            'quantity_available' => 2,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.loans.create', [
                'student_id' => $student->id,
                'professional_id' => $professional->id,
                'item_id' => $item->id,
                'item_type' => $item->getMorphClass(),
            ]));

        $response->assertOk();
        $response->assertViewIs('pages.inclusive-radar.loans.create');
        $response->assertViewHas('students');
        $response->assertViewHas('professionals');
        $response->assertViewHas('assistive_technologies');
        $response->assertViewHas('educational_materials');
        $response->assertViewHas('selectedStudentId', $student->id);
        $response->assertViewHas('selectedProfessionalId', $professional->id);
        $response->assertViewHas('selectedItemId', $item->id);
        $response->assertViewHas('selectedItemType', $item->getMorphClass());
    }

    public function test_admin_can_store_a_loan()
    {
        $student = Student::factory()->create();
        $item = AssistiveTechnology::factory()->physical()->available()->loanable()->create([
            'quantity' => 2,
            'quantity_available' => 2,
        ]);

        $data = [
            'loanable_id' => $item->id,
            'loanable_type' => 'assistive_technology',
            'student_id' => $student->id,
            'loan_date' => now()->toDateString(),
            'due_date' => now()->addDays(5)->toDateString(),
            'user_id' => $this->admin->id,
            'observation' => 'Emprestimo inicial',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('inclusive-radar.loans.store'), $data);

        $response->assertRedirect(route('inclusive-radar.loans.index'));
        $this->assertDatabaseHas('loans', [
            'loanable_id' => $item->id,
            'loanable_type' => $item->getMorphClass(),
            'student_id' => $student->id,
        ]);
    }

    public function test_admin_can_view_a_loan()
    {
        $loan = Loan::factory()->create([
            'status' => LoanStatus::ACTIVE,
            'due_date' => now()->subDay(),
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.loans.show', $loan));

        $response->assertOk();
        $response->assertViewIs('pages.inclusive-radar.loans.show');
        $response->assertViewHas('loan');
        $response->assertViewHas('isOverdue', true);
    }

    public function test_admin_can_access_loan_edit_page()
    {
        $loan = Loan::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.loans.edit', $loan));

        $response->assertOk();
        $response->assertViewIs('pages.inclusive-radar.loans.edit');
        $response->assertViewHas('loan');
        $response->assertViewHas('students');
        $response->assertViewHas('professionals');
    }

    public function test_admin_can_update_a_loan_observation()
    {
        $loan = Loan::factory()->create(['observation' => 'Antiga']);
        $loanable = $loan->loanable;

        $response = $this->actingAs($this->admin)
            ->put(route('inclusive-radar.loans.update', $loan), [
                'loanable_id' => $loan->loanable_id,
                'loanable_type' => $loanable->getMorphClass(),
                'student_id' => $loan->student_id,
                'professional_id' => $loan->professional_id,
                'user_id' => $loan->user_id,
                'loan_date' => $loan->loan_date->toDateString(),
                'due_date' => $loan->due_date->toDateString(),
                'observation' => 'Nova observacao',
            ]);

        $response->assertRedirect(route('inclusive-radar.loans.index'));
        $this->assertDatabaseHas('loans', [
            'id' => $loan->id,
            'observation' => 'Nova observacao',
        ]);
    }

    public function test_admin_can_register_loan_return()
    {
        $loan = Loan::factory()->create([
            'status' => LoanStatus::ACTIVE,
            'return_date' => null,
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('inclusive-radar.loans.return', $loan), [
                'is_damaged' => false,
                'observation' => 'Devolvido em bom estado',
            ]);

        $response->assertRedirect(route('inclusive-radar.loans.index'));
        $this->assertNotNull($loan->fresh()->return_date);
    }

    public function test_admin_can_delete_a_returned_loan()
    {
        $loan = Loan::factory()->returned()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('inclusive-radar.loans.destroy', $loan));

        $response->assertRedirect(route('inclusive-radar.loans.index'));
        $this->assertDatabaseMissing('loans', [
            'id' => $loan->id,
        ]);
    }

    public function test_admin_can_generate_loan_pdf()
    {
        $loan = Loan::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('inclusive-radar.loans.pdf', $loan));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }
}
