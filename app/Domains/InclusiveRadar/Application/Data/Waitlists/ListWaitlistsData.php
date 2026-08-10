<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Data\Waitlists;

use App\Domains\InclusiveRadar\Domain\Enums\WaitlistStatus;
use Illuminate\Validation\Rules\Enum;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MergeValidationRules]
final class ListWaitlistsData extends Data
{
    public function __construct(
        public ?string $item = null,
        public ?string $student = null,
        public ?string $professional = null,
        public ?WaitlistStatus $status = null,
        public ?string $startDate = null,
        public ?string $endDate = null,
        public int $perPage = 10,
    ) {}

    public static function rules(): array
    {
        return [
            'item' => ['nullable', 'string', 'max:255'],
            'student' => ['nullable', 'string', 'max:255'],
            'professional' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', new Enum(WaitlistStatus::class)],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'per_page' => ['integer', 'min:1', 'max:100'],
        ];
    }
}
