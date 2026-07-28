<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\DTOs\PeiDisciplines;

final readonly class UpdatePeiDisciplineDTO
{
    public function __construct(
        public string $specificObjectives,
        public string $contentProgrammatic,
        public string $methodologies,
        public string $evaluations,
        public ?string $opinion = null,
        public ?string $complementaryRecords = null,
    ) {}
}
