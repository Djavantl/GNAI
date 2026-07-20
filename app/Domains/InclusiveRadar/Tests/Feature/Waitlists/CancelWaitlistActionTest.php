<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Feature\Waitlists;

use App\Domains\InclusiveRadar\Application\Actions\Waitlists\CancelWaitlistAction;
use App\Domains\InclusiveRadar\Domain\Enums\WaitlistStatus;
use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;
use App\Domains\InclusiveRadar\Domain\Models\Waitlist;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CancelWaitlistActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_cancels_a_waitlist(): void
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

        $cancelled = app(CancelWaitlistAction::class)->execute($waitlist);

        self::assertSame(WaitlistStatus::CANCELLED, $cancelled->status);
        $this->assertDatabaseHas('waitlists', [
            'id' => $waitlist->id,
            'status' => WaitlistStatus::CANCELLED->value,
        ]);
    }
}
