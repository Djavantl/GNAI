<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Data\StudentCourses;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class ListStudentCoursesData extends Data
{
    public function __construct(
        public ?int $courseId = null,
        public ?string $academicYear = null,
        public ?bool $isCurrent = null,
        public int $perPage = 10,
    ) {}

    public static function rules(): array
    {
        return [
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
            'academic_year' => ['nullable', 'digits:4'],
            'is_current' => ['nullable', 'boolean'],
            'per_page' => ['integer', 'min:1', 'max:100'],
        ];
    }
}
