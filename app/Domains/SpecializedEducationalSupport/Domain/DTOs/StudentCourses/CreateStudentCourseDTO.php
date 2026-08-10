<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\DTOs\StudentCourses;

final readonly class CreateStudentCourseDTO
{
    public function __construct(
        public int $academicYear,
        public bool $isCurrent = false,
        public ?string $schoolAttendanceStatus = null,
    ) {}
}
