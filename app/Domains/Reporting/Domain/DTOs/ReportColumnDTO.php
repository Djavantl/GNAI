<?php

declare(strict_types=1);

namespace App\Domains\Reporting\Domain\DTOs;

use App\Domains\Reporting\Domain\Enums\ReportColumnType;

final readonly class ReportColumnDTO
{
    /** @param array<string, string> $options */
    public function __construct(
        public string $key,
        public string $label,
        public ReportColumnType $type = ReportColumnType::TEXT,
        public array $options = [],
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'label' => $this->label,
            'type' => $this->type->value,
            'options' => $this->options,
        ];
    }
}
