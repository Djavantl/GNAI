<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Data\Sessions;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class ListSessionsData extends Data
{
    public function __construct(
        public ?int $student = null,
        public ?int $professional = null,
        public ?string $type = null,
        public ?string $status = null,
        public ?string $week = null,
        public ?int $studentAgenda = null,
        public ?int $professionalAgenda = null,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        foreach (['student', 'professional', 'student_agenda', 'professional_agenda'] as $field) {
            if (($properties[$field] ?? null) === '') {
                $properties[$field] = null;
            }
        }

        return $properties;
    }
}
