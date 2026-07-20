<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Feature\BarrierCategories;

use App\Domains\InclusiveRadar\Application\Actions\BarrierCategories\DeleteBarrierCategoryAction;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidBarrierCategory;
use App\Domains\InclusiveRadar\Domain\Models\BarrierCategory;
use App\Models\InclusiveRadar\Barrier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class DeleteBarrierCategoryActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_deletes_a_category_without_barriers(): void
    {
        $category = BarrierCategory::factory()->create();

        app(DeleteBarrierCategoryAction::class)->execute($category);

        $this->assertSoftDeleted('barrier_categories', [
            'id' => $category->id,
        ]);
    }

    public function test_it_rejects_deleting_a_category_with_blocking_barriers(): void
    {
        $category = BarrierCategory::factory()->create();
        Barrier::factory()->create([
            'barrier_category_id' => $category->id,
        ]);

        $this->expectException(InvalidBarrierCategory::class);
        $this->expectExceptionMessage('Esta categoria não pode ser excluída pois possui barreiras ativas.');

        app(DeleteBarrierCategoryAction::class)->execute($category);
    }
}
