<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Feature\Waitlists;

use App\Domains\Inclusion\Application\Queries\Waitlists\WaitlistFormQuery;
use App\Domains\Inclusion\Domain\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class WaitlistFormQueryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_only_unavailable_physical_loanable_items(): void
    {
        $waitlistable = Item::factory()->physical()->loanable()->unavailable()->create();
        Item::factory()->physical()->loanable()->available()->create();
        Item::factory()->digital()->create();
        Item::factory()->physical()->notLoanable()->unavailable()->create();
        Item::factory()->physical()->loanable()->unavailable()->inactive()->create();

        $items = app(WaitlistFormQuery::class)->forCreation()['items'];

        self::assertSame([$waitlistable->id], $items->modelKeys());
    }
}
