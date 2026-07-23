<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Data\InstitutionalEvents;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class ListInstitutionalEventsData extends Data
{
    public function __construct(
        public ?string $title = null,
        public ?bool $isActive = null,
        public int $perPage = 10,
    ) {}

    public static function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'per_page' => ['integer', 'min:1', 'max:100'],
        ];
    }
}
