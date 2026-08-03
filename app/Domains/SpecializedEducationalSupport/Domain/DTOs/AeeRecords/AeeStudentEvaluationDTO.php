<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\DTOs\AeeRecords;

final readonly class AeeStudentEvaluationDTO
{
    public function __construct(
        public int $studentId,
        public bool $isPresent,
        public ?string $absenceReason,
        public ?string $adaptationsMade,
        public ?string $studentParticipation,
        public ?string $developmentEvaluation,
        public ?string $progressIndicators,
        public ?string $recommendations,
        public ?string $nextSessionAdjustments,
    ) {}
}
