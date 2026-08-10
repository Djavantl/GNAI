<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Data\AssistiveTechnologies;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class ListAssistiveTechnologiesData extends Data
{
    public function __construct(
        public ?string $name = null,
        public ?bool $isDigital = null,
        public ?bool $isActive = null,
        public ?bool $available = null,
        public int $perPage = 10,
    ) {}

    public static function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'is_digital' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'available' => ['nullable', 'boolean'],
            'per_page' => ['integer', 'min:1', 'max:100'],
        ];
    }
}
