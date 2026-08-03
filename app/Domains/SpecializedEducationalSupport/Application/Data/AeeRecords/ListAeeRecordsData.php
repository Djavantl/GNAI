<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Data\AeeRecords;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class ListAeeRecordsData extends Data
{
    public function __construct(
        public ?int $student = null,
        public ?int $professionalId = null,
        public ?bool $isPresent = null,
        public int $perPage = 10,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        foreach (['student', 'professional_id', 'is_present'] as $field) {
            if (($properties[$field] ?? null) === '') {
                $properties[$field] = null;
            }
        }

        return $properties;
    }

    public static function rules(): array
    {
        return [
            'student' => ['nullable', 'integer', 'exists:students,id'],
            'professional_id' => ['nullable', 'integer', 'exists:professionals,id'],
            'is_present' => ['nullable', 'boolean'],
            'per_page' => ['integer', 'min:1', 'max:100'],
        ];
    }
}
