<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Data\Loans;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MergeValidationRules]
final class ReturnLoanData extends Data
{
    public function __construct(
        public bool $isDamaged = false,
        public ?string $observation = null,
    ) {}

    public static function rules(): array
    {
        return [
            'is_damaged' => ['nullable', 'boolean'],
            'observation' => ['nullable', 'string'],
        ];
    }
}
