<?php

declare(strict_types=1);

namespace App\Domains\Reporting\Domain\DTOs;

final readonly class ReportRequestDTO
{
    /**
     * @param  list<string>  $columns
     * @param  list<ReportFilterDTO>  $filters
     */
    public function __construct(
        public string $source,
        public array $columns,
        public array $filters,
        public int $limit = 200,
        public bool $filterRelatedValues = false,
    ) {}
}
