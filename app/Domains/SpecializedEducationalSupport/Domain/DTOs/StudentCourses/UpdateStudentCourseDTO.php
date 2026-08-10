<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\DTOs\StudentCourses;

final readonly class UpdateStudentCourseDTO
{
    public function __construct(
        public int $academicYear,
        public ?string $schoolAttendanceStatus = null,
    ) {}
}
