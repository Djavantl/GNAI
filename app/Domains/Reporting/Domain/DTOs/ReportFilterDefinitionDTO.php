<?php

declare(strict_types=1);

namespace App\Domains\Reporting\Domain\DTOs;

use App\Domains\Reporting\Domain\Enums\ReportColumnType;
use App\Domains\Reporting\Domain\Enums\ReportOperator;

final readonly class ReportFilterDefinitionDTO
{
    /**
     * @param  list<ReportOperator>  $operators
     * @param  array<string, string>  $options
     */
    public function __construct(
        public string $key,
        public string $label,
        public ReportColumnType $type,
        public array $operators,
        public array $options = [],
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'label' => $this->label,
            'type' => $this->type->value,
            'operators' => array_map(
                static fn (ReportOperator $operator): string => $operator->value,
                $this->operators,
            ),
            'options' => $this->options,
        ];
    }
}
