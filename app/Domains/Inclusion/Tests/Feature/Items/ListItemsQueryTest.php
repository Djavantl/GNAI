<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Feature\Items;

use App\Domains\Inclusion\Application\Data\Items\ListItemsData;
use App\Domains\Inclusion\Application\Queries\Items\ListItemsQuery;
use App\Domains\Inclusion\Domain\Enums\ItemType;
use App\Domains\Inclusion\Domain\Enums\ResourceStatus;
use App\Domains\Inclusion\Domain\Models\AccessibilityFeature;
use App\Domains\Inclusion\Domain\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ListItemsQueryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_filters_by_item_type(): void
    {
        $technology = Item::factory()->assistiveTechnology()->create();
        Item::factory()->accessibleEducationalMaterial()->create();

        $items = app(ListItemsQuery::class)->execute(
            new ListItemsData(itemType: ItemType::AssistiveTechnology),
        );

        self::assertSame([$technology->id], $items->getCollection()->modelKeys());
    }

    public function test_it_combines_name_stock_status_and_activity_filters(): void
    {
        $matching = Item::factory()->physical()->unavailable()->inactive()->create([
            'name' => 'Livro Braille inativo',
            'quantity' => 1,
        ]);
        Item::factory()->physical()->available()->inactive()->create(['name' => 'Livro Braille com estoque']);
        Item::factory()->physical()->unavailable()->active()->create(['name' => 'Livro Braille ativo']);
        Item::factory()->digital()->inactive()->withStatus(ResourceStatus::UNAVAILABLE)->create([
            'name' => 'Livro Braille digital',
        ]);
        Item::factory()->physical()->unavailable()->inactive()->create(['name' => 'Lupa eletrônica']);

        $items = app(ListItemsQuery::class)->execute(new ListItemsData(
            name: ' Braille ',
            status: ResourceStatus::UNAVAILABLE,
            isDigital: false,
            isActive: false,
            available: false,
        ));

        self::assertSame([$matching->id], $items->getCollection()->modelKeys());
    }

    public function test_it_filters_by_accessibility_feature(): void
    {
        $feature = AccessibilityFeature::factory()->create();
        $matching = Item::factory()->create();
        $matching->accessibilityFeatures()->sync([$feature->id]);
        Item::factory()->withAccessibilityFeatures()->create();

        $items = app(ListItemsQuery::class)->execute(
            new ListItemsData(accessibilityFeatureId: $feature->id),
        );

        self::assertSame([$matching->id], $items->getCollection()->modelKeys());
    }
}
