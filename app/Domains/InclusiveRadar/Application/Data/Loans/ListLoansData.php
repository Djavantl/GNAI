<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Data\Loans;

use App\Domains\InclusiveRadar\Domain\Enums\LoanStatus;
use Illuminate\Validation\Rules\Enum;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MergeValidationRules]
final class ListLoansData extends Data
{
    public function __construct(
        public ?string $student = null,
        public ?string $professional = null,
        public ?string $item = null,
        public ?LoanStatus $status = null,
        public int $perPage = 10,
    ) {}

    public static function rules(): array
    {
        return [
            'student' => ['nullable', 'string', 'max:255'],
            'professional' => ['nullable', 'string', 'max:255'],
            'item' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', new Enum(LoanStatus::class)],
            'per_page' => ['integer', 'min:1', 'max:100'],
        ];
    }
}
