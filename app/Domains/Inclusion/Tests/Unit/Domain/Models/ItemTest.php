<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Unit\Domain\Models;

use App\Domains\Inclusion\Domain\Exceptions\InvalidItem;
use App\Domains\Inclusion\Domain\Models\Item;
use Tests\TestCase;

final class ItemTest extends TestCase
{
    public function test_it_restores_stock_only_for_physical_items(): void
    {
        $physical = new Item(['is_digital' => false, 'quantity' => 5, 'quantity_available' => 3]);
        $digital = new Item(['is_digital' => true, 'quantity' => null, 'quantity_available' => null]);

        self::assertSame(2, $physical->stock()->borrowed());
        self::assertTrue($digital->stock()->isNotApplicable());
    }

    public function test_it_rejects_target_audience_on_an_unsaved_item(): void
    {
        $this->expectException(InvalidItem::class);
        $this->expectExceptionMessage('O público-alvo só pode ser atribuído a um item persistido.');

        (new Item)->assignTargetAudience([1]);
    }

    public function test_it_rejects_accessibility_features_on_an_unsaved_item(): void
    {
        $this->expectException(InvalidItem::class);
        $this->expectExceptionMessage('Os recursos de acessibilidade só podem ser atribuídos a um item persistido.');

        (new Item)->assignAccessibilityFeatures([1]);
    }
}
