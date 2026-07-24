<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Feature\Waitlists;

use App\Domains\InclusiveRadar\Application\Actions\Waitlists\CreateWaitlistAction;
use App\Domains\InclusiveRadar\Application\Data\Waitlists\CreateWaitlistData;
use App\Domains\InclusiveRadar\Domain\Enums\LoanableType;
use App\Domains\InclusiveRadar\Domain\Enums\LoanStatus;
use App\Domains\InclusiveRadar\Domain\Enums\ResourceStatus;
use App\Domains\InclusiveRadar\Domain\Enums\WaitlistStatus;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidWaitlist;
use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;
use App\Domains\InclusiveRadar\Domain\Models\Loan;
use App\Domains\InclusiveRadar\Domain\Models\Waitlist;
use App\Models\SpecializedEducationalSupport\Student;
use App\Domains\Auth\Domain\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CreateWaitlistActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_waitlist_for_an_unavailable_item(): void
    {
        $user = User::factory()->create();
        $student = Student::factory()->create();
        $technology = $this->technology(
            quantityAvailable: 0,
            status: ResourceStatus::AVAILABLE,
        );

        $waitlist = app(CreateWaitlistAction::class)->execute(
            data: new CreateWaitlistData(
                waitlistableId: $technology->id,
                waitlistableType: LoanableType::AssistiveTechnology,
                studentId: $student->id,
                professionalId: null,
                observation: 'Aguardar disponibilidade.',
            ),
            registeredBy: $user->id,
        );

        self::assertSame(WaitlistStatus::WAITING, $waitlist->status);
        $this->assertDatabaseHas('waitlists', [
            'id' => $waitlist->id,
            'waitlistable_id' => $technology->id,
            'waitlistable_type' => 'assistive_technology',
            'student_id' => $student->id,
            'professional_id' => null,
            'user_id' => $user->id,
            'status' => WaitlistStatus::WAITING->value,
            'observation' => 'Aguardar disponibilidade.',
        ]);
    }

    public function test_it_rejects_waitlist_when_item_is_available(): void
    {
        $user = User::factory()->create();
        $student = Student::factory()->create();
        $technology = $this->technology(
            quantityAvailable: 1,
            status: ResourceStatus::AVAILABLE,
        );

        $this->expectException(InvalidWaitlist::class);
        $this->expectExceptionMessage(
            'Este recurso ainda possui unidades disponíveis e pode ser emprestado, portanto não é possível criar uma fila de espera.'
        );

        app(CreateWaitlistAction::class)->execute(
            data: new CreateWaitlistData(
                waitlistableId: $technology->id,
                waitlistableType: LoanableType::AssistiveTechnology,
                studentId: $student->id,
                professionalId: null,
            ),
            registeredBy: $user->id,
        );
    }

    public function test_it_rejects_duplicate_active_waitlist(): void
    {
        $user = User::factory()->create();
        $student = Student::factory()->create();
        $technology = $this->technology(
            quantityAvailable: 0,
            status: ResourceStatus::AVAILABLE,
        );
        $this->waitlist($technology, $user, $student, WaitlistStatus::WAITING);

        $this->expectException(InvalidWaitlist::class);
        $this->expectExceptionMessage('Este beneficiário já possui uma solicitação ativa para este recurso.');

        app(CreateWaitlistAction::class)->execute(
            data: new CreateWaitlistData(
                waitlistableId: $technology->id,
                waitlistableType: LoanableType::AssistiveTechnology,
                studentId: $student->id,
                professionalId: null,
            ),
            registeredBy: $user->id,
        );
    }

    public function test_it_rejects_waitlist_when_beneficiary_has_open_loan(): void
    {
        $user = User::factory()->create();
        $student = Student::factory()->create();
        $technology = $this->technology(
            quantityAvailable: 0,
            status: ResourceStatus::AVAILABLE,
        );

        Loan::factory()
            ->forAssistiveTechnology($technology)
            ->forStudent($student)
            ->state([
                'user_id' => $user->id,
                'loan_date' => now()->subDay(),
                'due_date' => now()->addWeek(),
                'return_date' => null,
                'status' => LoanStatus::ACTIVE,
            ])
            ->create();

        $this->expectException(InvalidWaitlist::class);
        $this->expectExceptionMessage('Este beneficiário já possui um empréstimo ativo deste recurso.');

        app(CreateWaitlistAction::class)->execute(
            data: new CreateWaitlistData(
                waitlistableId: $technology->id,
                waitlistableType: LoanableType::AssistiveTechnology,
                studentId: $student->id,
                professionalId: null,
            ),
            registeredBy: $user->id,
        );
    }

    private function technology(
        int $quantityAvailable = 0,
        ResourceStatus $status = ResourceStatus::AVAILABLE,
    ): AssistiveTechnology {
        return AssistiveTechnology::factory()
            ->physical()
            ->loanable()
            ->state([
                'name' => 'Linha Braille',
                'quantity' => 1,
                'quantity_available' => $quantityAvailable,
                'status' => $status,
            ])
            ->create();
    }

    private function waitlist(
        AssistiveTechnology $technology,
        User $user,
        Student $student,
        WaitlistStatus $status,
    ): Waitlist {
        return Waitlist::factory()
            ->forAssistiveTechnology($technology)
            ->forStudent($student)
            ->state([
                'user_id' => $user->id,
                'status' => $status,
                'observation' => null,
            ])
            ->create();
    }
}
