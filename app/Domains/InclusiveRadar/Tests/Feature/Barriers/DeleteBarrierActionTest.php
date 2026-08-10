<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Feature\Barriers;

use App\Domains\InclusiveRadar\Application\Actions\Barriers\DeleteBarrierAction;
use App\Domains\InclusiveRadar\Domain\Models\Barrier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class DeleteBarrierActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_deletes_a_barrier(): void
    {
        $barrier = Barrier::factory()->create();

        app(DeleteBarrierAction::class)->execute($barrier);

        $this->assertDatabaseMissing('barriers', [
            'id' => $barrier->id,
        ]);
    }
}
