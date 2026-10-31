<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Data\StudentDeficiencies;

use App\Domains\Inclusion\Domain\Enums\DeficiencySeverity;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class ListStudentDeficienciesData extends Data
{
    public function __construct(
        public ?int $deficiencyId = null,
        public ?DeficiencySeverity $severity = null,
        public int $perPage = 10,
    ) {}

    public static function rules(): array
    {
        return [
            'deficiency_id' => ['nullable', 'integer', 'exists:deficiencies,id'],
            'severity' => ['nullable', Rule::enum(DeficiencySeverity::class)],
            'per_page' => ['integer', 'min:1', 'max:100'],
        ];
    }
}
