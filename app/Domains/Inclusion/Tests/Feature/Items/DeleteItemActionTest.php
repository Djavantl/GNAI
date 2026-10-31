<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Feature\Items;

use App\Domains\Inclusion\Application\Actions\Items\DeleteItemAction;
use App\Domains\Inclusion\Domain\Exceptions\ResourceHasOpenLoans;
use App\Domains\Inclusion\Domain\Models\Inspection;
use App\Domains\Inclusion\Domain\Models\Item;
use App\Domains\Inclusion\Domain\Models\Loan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class DeleteItemActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_soft_deletes_the_item_preserving_its_history(): void
    {
        $item = Item::factory()->physical()->withDeficiencies()->create();
        $inspection = Inspection::factory()->forAssistiveTechnology($item)->create();

        app(DeleteItemAction::class)->execute($item);

        $this->assertSoftDeleted('items', ['id' => $item->id]);
        $this->assertDatabaseHas('item_deficiency', ['item_id' => $item->id]);
        $this->assertDatabaseHas('inspections', ['id' => $inspection->id]);
    }

    public function test_it_rejects_deletion_while_there_is_an_open_loan(): void
    {
        $item = Item::factory()->physical()->create();
        Loan::factory()->forAssistiveTechnology($item)->create();

        try {
            app(DeleteItemAction::class)->execute($item);
            self::fail('Era esperado ResourceHasOpenLoans.');
        } catch (ResourceHasOpenLoans) {
            $this->assertNotSoftDeleted('items', ['id' => $item->id]);
        }
    }

    public function test_it_deletes_an_item_whose_loans_were_all_returned(): void
    {
        $item = Item::factory()->physical()->create();
        Loan::factory()->forAssistiveTechnology($item)->returned()->create();

        app(DeleteItemAction::class)->execute($item);

        $this->assertSoftDeleted('items', ['id' => $item->id]);
    }
}
