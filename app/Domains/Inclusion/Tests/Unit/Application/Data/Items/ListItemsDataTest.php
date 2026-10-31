<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Unit\Application\Data\Items;

use App\Domains\Inclusion\Application\Data\Items\ListItemsData;
use App\Domains\Inclusion\Domain\Enums\ItemType;
use App\Domains\Inclusion\Domain\Enums\ResourceStatus;
use Tests\TestCase;

final class ListItemsDataTest extends TestCase
{
    public function test_it_maps_filters_and_preserves_false_values(): void
    {
        $data = ListItemsData::from([
            'name' => 'Braille',
            'item_type' => ItemType::AssistiveTechnology->value,
            'status' => ResourceStatus::UNAVAILABLE->value,
            'is_digital' => false,
            'is_active' => false,
            'available' => false,
            'accessibility_feature_id' => 4,
            'per_page' => 25,
        ]);

        self::assertSame(ItemType::AssistiveTechnology, $data->itemType);
        self::assertSame(ResourceStatus::UNAVAILABLE, $data->status);
        self::assertFalse($data->isDigital);
        self::assertFalse($data->isActive);
        self::assertFalse($data->available);
        self::assertSame(4, $data->accessibilityFeatureId);
        self::assertSame(25, $data->perPage);
    }

    public function test_it_leaves_absent_filters_empty(): void
    {
        $data = ListItemsData::from([]);

        self::assertNull($data->itemType);
        self::assertNull($data->isDigital);
        self::assertNull($data->available);
        self::assertSame(10, $data->perPage);
    }
}
