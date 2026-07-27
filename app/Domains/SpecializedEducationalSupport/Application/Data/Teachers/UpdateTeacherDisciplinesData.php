<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Data\Teachers;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MergeValidationRules]
final class UpdateTeacherDisciplinesData extends Data
{
    /**
     * @param array<int|string, list<int|string>> $assignments
     */
    public function __construct(
        public array $assignments = [],
    ) {}

    public static function rules(): array
    {
        return [
            'assignments' => ['nullable', 'array'],
        ];
    }
}
