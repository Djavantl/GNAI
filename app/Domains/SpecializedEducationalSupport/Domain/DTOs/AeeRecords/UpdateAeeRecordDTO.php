<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\DTOs\AeeRecords;

final readonly class UpdateAeeRecordDTO
{
    /** @param list<AeeStudentEvaluationDTO> $evaluations */
    public function __construct(
        public string $duration,
        public string $activitiesPerformed,
        public ?string $strategiesUsed,
        public ?string $resourcesUsed,
        public ?string $generalObservations,
        public array $evaluations,
    ) {}
}
