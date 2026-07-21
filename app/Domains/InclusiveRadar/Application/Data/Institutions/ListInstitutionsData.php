<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Data\Institutions;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class ListInstitutionsData extends Data
{
    public function __construct(
        public ?string $name = null,
        public ?string $location = null,
        public ?bool $isActive = null,
        public int $perPage = 10,
    ) {}

    public static function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'per_page' => ['integer', 'min:1', 'max:100'],
        ];
    }
}
