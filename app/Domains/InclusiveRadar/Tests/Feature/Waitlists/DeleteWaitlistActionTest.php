<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Feature\Waitlists;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\InclusiveRadar\Application\Actions\Waitlists\DeleteWaitlistAction;
use App\Domains\InclusiveRadar\Domain\Enums\WaitlistStatus;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidWaitlist;
use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;
use App\Domains\InclusiveRadar\Domain\Models\Waitlist;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class DeleteWaitlistActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_deletes_a_non_fulfilled_waitlist(): void
    {
        $user = User::factory()->create();
        $student = Student::factory()->create();
        $technology = AssistiveTechnology::factory()->physical()->loanable()->unavailable()->create();
        $waitlist = Waitlist::factory()
            ->forAssistiveTechnology($technology)
            ->forStudent($student)
            ->state([
                'user_id' => $user->id,
                'status' => WaitlistStatus::WAITING,
            ])
            ->create();

        app(DeleteWaitlistAction::class)->execute($waitlist);

        $this->assertDatabaseMissing('waitlists', [
            'id' => $waitlist->id,
        ]);
    }

    public function test_it_rejects_deleting_a_fulfilled_waitlist(): void
    {
        $user = User::factory()->create();
        $student = Student::factory()->create();
        $technology = AssistiveTechnology::factory()->physical()->loanable()->unavailable()->create();
        $waitlist = Waitlist::factory()
            ->forAssistiveTechnology($technology)
            ->forStudent($student)
            ->state([
                'user_id' => $user->id,
                'status' => WaitlistStatus::FULFILLED,
            ])
            ->create();

        $this->expectException(InvalidWaitlist::class);
        $this->expectExceptionMessage('Solicitações já atendidas não podem ser removidas.');

        app(DeleteWaitlistAction::class)->execute($waitlist);
    }
}
