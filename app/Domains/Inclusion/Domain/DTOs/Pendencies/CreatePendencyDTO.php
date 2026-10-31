<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\DTOs\Pendencies;

use App\Domains\Inclusion\Domain\Enums\Priority;

final readonly class CreatePendencyDTO
{
    public function __construct(
        public string $title,
        public Priority $priority,
        public ?string $description = null,
        public ?string $dueDate = null,
    ) {}
}
