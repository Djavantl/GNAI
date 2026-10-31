<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Data\Publications;

use App\Domains\Inclusion\Domain\Enums\PublicationType;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class ListPublicationsData extends Data
{
    public function __construct(
        public ?string $title = null,
        public ?bool $isActive = null,
        public ?string $type = null,
        public int $perPage = 10,
    ) {}

    public static function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'type' => ['nullable', Rule::enum(PublicationType::class)],
            'per_page' => ['integer', 'min:1', 'max:100'],
        ];
    }
}
