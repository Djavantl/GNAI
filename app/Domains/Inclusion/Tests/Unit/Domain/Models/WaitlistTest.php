<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Unit\Domain\Models;

use App\Domains\Inclusion\Domain\DTOs\Waitlists\UpdateWaitlistDTO;
use App\Domains\Inclusion\Domain\Enums\WaitlistStatus;
use App\Domains\Inclusion\Domain\Exceptions\InvalidWaitlist;
use App\Domains\Inclusion\Domain\Models\Waitlist;
use Tests\TestCase;

final class WaitlistTest extends TestCase
{
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

    public function test_it_allows_revising_observation_of_a_finalized_waitlist(): void
    {
        $waitlist = new Waitlist([
            'status' => WaitlistStatus::CANCELLED,
            'observation' => 'Antiga.',
        ]);

        $waitlist->revise(new UpdateWaitlistDTO(
            status: null,
            observation: 'Nova.',
        ));

        self::assertSame(WaitlistStatus::CANCELLED, $waitlist->status);
        self::assertSame('Nova.', $waitlist->observation);
    }
}
