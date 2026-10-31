<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Feature\Waitlists;

use App\Domains\Inclusion\Application\Actions\Waitlists\UpdateWaitlistAction;
use App\Domains\Inclusion\Application\Data\Waitlists\UpdateWaitlistData;
use App\Domains\Inclusion\Domain\Enums\WaitlistStatus;
use App\Domains\Inclusion\Domain\Models\Item;
use App\Domains\Inclusion\Domain\Models\Waitlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class UpdateWaitlistActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_updates_status_and_observation(): void
    {
        $item = Item::factory()->physical()->loanable()->unavailable()->create();
        $waitlist = Waitlist::factory()->forAssistiveTechnology($item)->create([
            'status' => WaitlistStatus::WAITING,
        ]);

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
