<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Feature\BarrierCategories;

use App\Domains\InclusiveRadar\Application\Actions\BarrierCategories\UpdateBarrierCategoryAction;
use App\Domains\InclusiveRadar\Application\Data\BarrierCategories\UpdateBarrierCategoryData;
use App\Domains\InclusiveRadar\Domain\Models\BarrierCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class UpdateBarrierCategoryActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_updates_a_barrier_category(): void
    {
        $category = BarrierCategory::factory()->create([
            'name' => 'Arquitetônica',
            'description' => 'Antiga.',
            'blocks_map' => true,
            'is_active' => true,
        ]);

        $updated = app(UpdateBarrierCategoryAction::class)->execute(
            category: $category,
            data: new UpdateBarrierCategoryData(
                name: 'Comunicacional',
                description: 'Nova.',
                blocksMap: false,
                isActive: false,
            ),
        );

        self::assertSame('Comunicacional', $updated->name);
        $this->assertDatabaseHas('barrier_categories', [
            'id' => $category->id,
            'name' => 'Comunicacional',
            'description' => 'Nova.',
            'blocks_map' => false,
            'is_active' => false,
        ]);
    }
}
