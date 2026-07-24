<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Feature\Waitlists;

use App\Domains\InclusiveRadar\Application\Actions\Waitlists\UpdateWaitlistAction;
use App\Domains\InclusiveRadar\Application\Data\Waitlists\UpdateWaitlistData;
use App\Domains\InclusiveRadar\Domain\Enums\WaitlistStatus;
use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;
use App\Domains\InclusiveRadar\Domain\Models\Waitlist;
use App\Models\SpecializedEducationalSupport\Student;
use App\Domains\Auth\Domain\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class UpdateWaitlistActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_updates_a_waitlist(): void
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

        $updated = app(UpdateWaitlistAction::class)->execute(
            waitlist: $waitlist,
            data: new UpdateWaitlistData(
                status: WaitlistStatus::NOTIFIED,
                observation: 'Beneficiário notificado.',
            ),
        );

        self::assertSame(WaitlistStatus::NOTIFIED, $updated->status);
        $this->assertDatabaseHas('waitlists', [
            'id' => $waitlist->id,
            'status' => WaitlistStatus::NOTIFIED->value,
            'observation' => 'Beneficiário notificado.',
        ]);
    }
}
