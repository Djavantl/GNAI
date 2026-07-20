<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Feature\BarrierCategories;

use App\Domains\InclusiveRadar\Application\Actions\BarrierCategories\CreateBarrierCategoryAction;
use App\Domains\InclusiveRadar\Application\Data\BarrierCategories\CreateBarrierCategoryData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CreateBarrierCategoryActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_barrier_category(): void
    {
        $category = app(CreateBarrierCategoryAction::class)->execute(
            new CreateBarrierCategoryData(
                name: 'Arquitetônica',
                description: 'Barreiras físicas.',
                blocksMap: false,
                isActive: true,
            ),
        );

        self::assertSame('Arquitetônica', $category->name);
        $this->assertDatabaseHas('barrier_categories', [
            'id' => $category->id,
            'name' => 'Arquitetônica',
            'description' => 'Barreiras físicas.',
            'blocks_map' => false,
            'is_active' => true,
        ]);
    }
}
