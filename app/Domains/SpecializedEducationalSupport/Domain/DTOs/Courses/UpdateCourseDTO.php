<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\DTOs\Courses;

final readonly class UpdateCourseDTO
{
    public function __construct(
        public string $name,
        public bool $isActive,
        public ?string $description = null,
    ) {}
}
