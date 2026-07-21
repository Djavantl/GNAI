<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Data\Institutions;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MergeValidationRules]
final class UpdateInstitutionData extends Data
{
    public function __construct(
        public string $name,
        public string $city,
        public string $state,
        public float $latitude,
        public float $longitude,
        public ?string $shortName = null,
        public ?string $district = null,
        public ?string $address = null,
        public ?int $defaultZoom = 16,
        public bool $isActive = false,
    ) {}

    public static function rules(): array
    {
        return CreateInstitutionData::rules();
    }

    public static function messages(): array
    {
        return CreateInstitutionData::messages();
    }
}
