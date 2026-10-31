<?php

declare(strict_types=1);

namespace App\Domains\Website\Application\Data;

use App\Domains\Inclusion\Domain\Enums\PublicationType;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class ListPublicPublicationsData extends Data
{
    public function __construct(
        public ?string $q = null,
        public ?string $type = null,
        public ?string $tag = null,
    ) {}

    public static function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:120'],
            'type' => ['nullable', Rule::enum(PublicationType::class)],
            'tag' => ['nullable', 'string', 'max:100'],
        ];
    }
}
