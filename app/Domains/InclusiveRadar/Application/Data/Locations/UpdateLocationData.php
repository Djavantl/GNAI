<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Data\Locations;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MergeValidationRules]
final class UpdateLocationData extends Data
{
    public function __construct(
        public int $institutionId,
        public string $name,
        public float $latitude,
        public float $longitude,
        public ?string $type = null,
        public ?string $description = null,
        public ?string $googlePlaceId = null,
        public bool $isActive = false,
    ) {}

    public static function rules(): array
    {
        return [
            'institution_id' => ['required', 'exists:institutions,id'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'google_place_id' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public static function messages(): array
    {
        return CreateLocationData::messages();
    }
}
