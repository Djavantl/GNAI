<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Unit\Domain\Models;

use App\Domains\InclusiveRadar\Domain\DTOs\Waitlists\CreateWaitlistDTO;
use App\Domains\InclusiveRadar\Domain\DTOs\Waitlists\UpdateWaitlistDTO;
use App\Domains\InclusiveRadar\Domain\Enums\LoanableType;
use App\Domains\InclusiveRadar\Domain\Enums\WaitlistStatus;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidWaitlist;
use App\Domains\InclusiveRadar\Domain\Models\Waitlist;
use Illuminate\Support\Carbon;
use Tests\TestCase;

final class WaitlistTest extends TestCase
{
    public function test_it_casts_status_to_domain_enum(): void
    {
        $waitlist = new Waitlist([
            'status' => WaitlistStatus::WAITING,
        ]);

        self::assertSame(WaitlistStatus::WAITING, $waitlist->status);
    }

    public function test_it_registers_a_waiting_waitlist(): void
    {
        $waitlist = Waitlist::register(new CreateWaitlistDTO(
            waitlistableId: 10,
            waitlistableType: LoanableType::AssistiveTechnology,
            studentId: 20,
            professionalId: null,
            registeredBy: 30,
            requestedAt: Carbon::parse('2026-01-01 10:00:00'),
            observation: 'Precisa do recurso.',
        ));

        self::assertSame(10, $waitlist->waitlistable_id);
        self::assertSame('assistive_technology', $waitlist->waitlistable_type);
        self::assertSame(20, $waitlist->student_id);
        self::assertNull($waitlist->professional_id);
        self::assertSame(30, $waitlist->user_id);
        self::assertSame(WaitlistStatus::WAITING, $waitlist->status);
        self::assertSame('Precisa do recurso.', $waitlist->observation);
        self::assertTrue($waitlist->requested_at->equalTo(Carbon::parse('2026-01-01 10:00:00')));
    }

    public function test_it_revises_status_and_observation(): void
    {
        $waitlist = new Waitlist([
            'status' => WaitlistStatus::WAITING,
            'observation' => 'Antiga.',
        ]);

        $waitlist->revise(new UpdateWaitlistDTO(
            status: WaitlistStatus::NOTIFIED,
            observation: 'Nova.',
        ));

        self::assertSame(WaitlistStatus::NOTIFIED, $waitlist->status);
        self::assertSame('Nova.', $waitlist->observation);
    }

    public function test_it_cancels_a_waiting_waitlist(): void
    {
        $waitlist = new Waitlist([
            'status' => WaitlistStatus::WAITING,
        ]);

        $waitlist->cancel();

        self::assertSame(WaitlistStatus::CANCELLED, $waitlist->status);
    }

    public function test_it_rejects_cancelling_a_non_waiting_waitlist(): void
    {
        $waitlist = new Waitlist([
            'status' => WaitlistStatus::NOTIFIED,
        ]);

        $this->expectException(InvalidWaitlist::class);
        $this->expectExceptionMessage('Apenas solicitações em espera podem ser canceladas.');

        $waitlist->cancel();
    }

    public function test_it_rejects_status_change_after_finalization(): void
    {
        $waitlist = new Waitlist([
            'status' => WaitlistStatus::FULFILLED,
        ]);

        $this->expectException(InvalidWaitlist::class);
        $this->expectExceptionMessage('Solicitação já finalizada não pode ter o status alterado.');

        $waitlist->revise(new UpdateWaitlistDTO(
            status: WaitlistStatus::WAITING,
            observation: 'Tentativa inválida.',
        ));
    }

    public function test_it_rejects_deleting_a_fulfilled_waitlist(): void
    {
        $waitlist = new Waitlist([
            'status' => WaitlistStatus::FULFILLED,
        ]);

        $this->expectException(InvalidWaitlist::class);
        $this->expectExceptionMessage('Solicitações já atendidas não podem ser removidas.');

        $waitlist->ensureCanBeDeleted();
    }
}
