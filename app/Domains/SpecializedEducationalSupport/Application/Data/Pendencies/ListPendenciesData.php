<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Data\Pendencies;

use App\Domains\SpecializedEducationalSupport\Domain\Enums\Priority;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class ListPendenciesData extends Data
{
    public function __construct(
        public ?string $title = null,
        public ?int $assignedTo = null,
        public ?Priority $priority = null,
        public ?bool $isCompleted = null,
        public int $perPage = 10,
    ) {}

    public static function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'assigned_to' => ['nullable', 'integer', Rule::exists('professionals', 'id')],
            'priority' => ['nullable', Rule::enum(Priority::class)],
            'is_completed' => ['nullable', 'boolean'],
            'per_page' => ['integer', 'min:1', 'max:100'],
        ];
    }
}
