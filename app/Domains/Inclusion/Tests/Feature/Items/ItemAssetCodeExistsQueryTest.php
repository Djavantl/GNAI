<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Feature\Items;

use App\Domains\Inclusion\Application\Queries\Items\ItemAssetCodeExistsQuery;
use App\Domains\Inclusion\Domain\Models\Item;
use App\Domains\Inclusion\Domain\ValueObjects\AssetCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ItemAssetCodeExistsQueryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_detects_an_asset_code_used_by_another_item(): void
    {
        $item = Item::factory()->physical()->create(['asset_code' => 'TA-1001']);
        $query = app(ItemAssetCodeExistsQuery::class);

        self::assertTrue($query->execute(AssetCode::from('TA-1001')));
        self::assertFalse($query->execute(AssetCode::from('TA-1001'), ignoreId: $item->id));
        self::assertFalse($query->execute(AssetCode::from('TA-9999')));
    }
}
