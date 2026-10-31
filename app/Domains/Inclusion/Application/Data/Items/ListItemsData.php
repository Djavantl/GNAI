<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Data\Items;

use App\Domains\Inclusion\Domain\Enums\ItemType;
use App\Domains\Inclusion\Domain\Enums\ResourceStatus;
use Illuminate\Validation\Rules\Enum;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class ListItemsData extends Data
{
    public function __construct(
        public ?string $name = null,
        public ?ItemType $itemType = null,
        public ?ResourceStatus $status = null,
        public ?bool $isDigital = null,
        public ?bool $isActive = null,
        public ?bool $available = null,
        public ?int $accessibilityFeatureId = null,
        public int $perPage = 10,
    ) {}

    public static function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'item_type' => ['nullable', new Enum(ItemType::class)],
            'status' => ['nullable'],
            'is_digital' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'available' => ['nullable', 'boolean'],
            'accessibility_feature_id' => ['nullable', 'integer', 'exists:accessibility_features,id'],
            'per_page' => ['integer', 'min:1', 'max:100'],
        ];
    }
}
