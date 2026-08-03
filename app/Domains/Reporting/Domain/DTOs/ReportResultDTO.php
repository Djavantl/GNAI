<?php

declare(strict_types=1);

namespace App\Domains\Reporting\Domain\DTOs;

final readonly class ReportResultDTO
{
    /**
     * @param  list<array<string, mixed>>  $rows
     * @param  array<string, string>  $headers
     */
    public function __construct(
        public array $rows,
        public array $headers,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'rows' => $this->rows,
            'headers' => $this->headers,
            'total' => count($this->rows),
        ];
    }
}
