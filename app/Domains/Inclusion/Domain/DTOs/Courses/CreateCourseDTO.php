<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\DTOs\Courses;

final readonly class CreateCourseDTO
{
    public function __construct(
        public string $name,
        public ?string $description = null,
        public bool $isActive = true,
    ) {}
}
