<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Feature\Waitlists;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\Inclusion\Application\Actions\Waitlists\CreateWaitlistAction;
use App\Domains\Inclusion\Application\Data\Waitlists\CreateWaitlistData;
use App\Domains\Inclusion\Domain\Enums\ItemType;
use App\Domains\Inclusion\Domain\Enums\LoanStatus;
use App\Domains\Inclusion\Domain\Enums\ResourceStatus;
use App\Domains\Inclusion\Domain\Enums\WaitlistStatus;
use App\Domains\Inclusion\Domain\Exceptions\InvalidLoanableResource;
use App\Domains\Inclusion\Domain\Exceptions\InvalidWaitlist;
use App\Domains\Inclusion\Domain\Models\Item;
use App\Domains\Inclusion\Domain\Models\Loan;
use App\Domains\Inclusion\Domain\Models\Student;
use App\Domains\Inclusion\Domain\Models\Waitlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CreateWaitlistActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_waiting_waitlist_for_an_item_without_stock(): void
    {
        $user = User::factory()->create();
        $student = Student::factory()->create();
        $item = $this->item(quantityAvailable: 0);

        $waitlist = app(CreateWaitlistAction::class)->execute(
            data: $this->data($item, $student, observation: 'Aguardar disponibilidade.'),
            registeredBy: $user->id,
        );

        self::assertSame(WaitlistStatus::WAITING, $waitlist->status);
        self::assertTrue($waitlist->waitlistable->is($item));
        $this->assertDatabaseHas('waitlists', [
            'id' => $waitlist->id,
            'waitlistable_id' => $item->id,
            'waitlistable_type' => 'item',
            'student_id' => $student->id,
            'professional_id' => null,
            'user_id' => $user->id,
            'status' => WaitlistStatus::WAITING->value,
            'observation' => 'Aguardar disponibilidade.',
        ]);
    }

    public function test_it_creates_a_waitlist_when_item_status_blocks_loans(): void
    {
        $user = User::factory()->create();
        $student = Student::factory()->create();
        $item = $this->item(quantityAvailable: 1, status: ResourceStatus::UNDER_MAINTENANCE);

        $waitlist = app(CreateWaitlistAction::class)->execute(
            data: $this->data($item, $student),
            registeredBy: $user->id,
        );

        self::assertSame(WaitlistStatus::WAITING, $waitlist->status);
    }

    public function test_it_rejects_waitlist_when_item_is_available(): void
    {
        $user = User::factory()->create();
        $student = Student::factory()->create();
        $item = $this->item(quantityAvailable: 1);

        $this->expectException(InvalidWaitlist::class);
        $this->expectExceptionMessage(
            'Este recurso ainda possui unidades disponíveis e pode ser emprestado, portanto não é possível criar uma fila de espera.'
        );

        app(CreateWaitlistAction::class)->execute(
            data: $this->data($item, $student),
            registeredBy: $user->id,
        );
    }

    public function test_it_rejects_waitlist_for_digital_item(): void
    {
        $user = User::factory()->create();
        $student = Student::factory()->create();
        $item = Item::factory()->assistiveTechnology()->digital()->create();

        $this->expectException(InvalidWaitlist::class);
        $this->expectExceptionMessage(
            'Recursos digitais não entram em fila de espera porque não possuem estoque físico.'
        );

        app(CreateWaitlistAction::class)->execute(
            data: $this->data($item, $student),
            registeredBy: $user->id,
        );
    }

    public function test_it_rejects_item_of_a_different_type(): void
    {
        $user = User::factory()->create();
        $student = Student::factory()->create();
        $item = $this->item(quantityAvailable: 0);

        $this->expectException(InvalidLoanableResource::class);

        app(CreateWaitlistAction::class)->execute(
            data: new CreateWaitlistData(
                waitlistableId: $item->id,
                itemType: ItemType::AccessibleEducationalMaterial,
                studentId: $student->id,
            ),
            registeredBy: $user->id,
        );
    }

    public function test_it_rejects_duplicate_active_waitlist(): void
    {
        $user = User::factory()->create();
        $student = Student::factory()->create();
        $item = $this->item(quantityAvailable: 0);
        Waitlist::factory()->forAssistiveTechnology($item)->forStudent($student)->notified()->create();

        $this->expectException(InvalidWaitlist::class);
        $this->expectExceptionMessage('Este beneficiário já possui uma solicitação ativa para este recurso.');

        app(CreateWaitlistAction::class)->execute(
            data: $this->data($item, $student),
            registeredBy: $user->id,
        );
    }

    public function test_it_allows_a_new_waitlist_after_a_cancelled_one(): void
    {
        $user = User::factory()->create();
        $student = Student::factory()->create();
        $item = $this->item(quantityAvailable: 0);
        Waitlist::factory()->forAssistiveTechnology($item)->forStudent($student)->cancelled()->create();

        $waitlist = app(CreateWaitlistAction::class)->execute(
            data: $this->data($item, $student),
            registeredBy: $user->id,
        );

        self::assertSame(WaitlistStatus::WAITING, $waitlist->status);
        $this->assertDatabaseCount('waitlists', 2);
    }

    public function test_it_rejects_waitlist_when_beneficiary_has_open_loan(): void
    {
        $user = User::factory()->create();
        $student = Student::factory()->create();
        $item = $this->item(quantityAvailable: 0);
        Loan::factory()->forAssistiveTechnology($item)->forStudent($student)->create([
            'status' => LoanStatus::ACTIVE,
        ]);

        $this->expectException(InvalidWaitlist::class);
        $this->expectExceptionMessage('Este beneficiário já possui um empréstimo ativo deste recurso.');

        app(CreateWaitlistAction::class)->execute(
            data: $this->data($item, $student),
            registeredBy: $user->id,
        );
    }

    private function item(int $quantityAvailable, ResourceStatus $status = ResourceStatus::AVAILABLE): Item
    {
        return Item::factory()
            ->assistiveTechnology()
            ->physical()
            ->loanable()
            ->create([
                'name' => 'Linha Braille',
                'quantity' => 1,
                'quantity_available' => $quantityAvailable,
                'status' => $status,
            ]);
    }

    private function data(Item $item, Student $student, ?string $observation = null): CreateWaitlistData
    {
        return new CreateWaitlistData(
            waitlistableId: $item->id,
            itemType: ItemType::AssistiveTechnology,
            studentId: $student->id,
            observation: $observation,
        );
    }
}
