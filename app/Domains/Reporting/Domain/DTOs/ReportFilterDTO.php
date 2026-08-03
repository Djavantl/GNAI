<?php

declare(strict_types=1);

namespace App\Domains\Reporting\Domain\DTOs;

use App\Domains\Reporting\Domain\Enums\ReportOperator;

final readonly class ReportFilterDTO
{
    public function __construct(
        public string $field,
        public ReportOperator $operator,
        public string|int|float|bool $value,
    ) {}
}
