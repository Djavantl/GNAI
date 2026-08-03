<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Data\PedagogicalRecords;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class ListPedagogicalRecordsData extends Data
{
    public function __construct(
        public ?int $student = null,
        public int $perPage = 10,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        if (($properties['student'] ?? null) === '') {
            $properties['student'] = null;
        }

        return $properties;
    }

    public static function rules(): array
    {
        return [
            'student' => ['nullable', 'integer', 'exists:students,id'],
            'per_page' => ['integer', 'min:1', 'max:100'],
        ];
    }
}
