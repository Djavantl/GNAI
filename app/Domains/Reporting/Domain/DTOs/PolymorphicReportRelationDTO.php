<?php

declare(strict_types=1);

namespace App\Domains\Reporting\Domain\DTOs;

final readonly class PolymorphicReportRelationDTO
{
    public function __construct(
        public string $key,
        public string $label,
        public string $eloquentRelation,
        public string $morphType,
        public string $sourceKey,
    ) {}
}
