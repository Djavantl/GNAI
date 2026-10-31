<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Data\StudentContexts;

use App\Domains\Inclusion\Application\Data\StudentContexts\Concerns\HasStudentContextValidation;
use App\Domains\Inclusion\Domain\Enums\AttentionLevel;
use App\Domains\Inclusion\Domain\Enums\AutonomyLevel;
use App\Domains\Inclusion\Domain\Enums\CommunicationType;
use App\Domains\Inclusion\Domain\Enums\GeneralLevel;
use App\Domains\Inclusion\Domain\Enums\InteractionLevel;
use App\Domains\Inclusion\Domain\Enums\MemoryLevel;
use App\Domains\Inclusion\Domain\Enums\ReasoningLevel;
use App\Domains\Inclusion\Domain\Enums\SocializationLevel;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class CreateStudentContextData extends Data
{
    use HasStudentContextValidation;

    public function __construct(
        public string $history,
        public string $specificEducationalNeeds,
        public string $knowledge,
        public string $difficulties,
        public ?GeneralLevel $learningLevel = null,
        public ?AttentionLevel $attentionLevel = null,
        public ?MemoryLevel $memoryLevel = null,
        public ?ReasoningLevel $reasoningLevel = null,
        public ?string $learningObservations = null,
        public ?CommunicationType $communicationType = null,
        public ?InteractionLevel $interactionLevel = null,
        public ?SocializationLevel $socializationLevel = null,
        public bool $showsAggressiveBehavior = false,
        public bool $showsWithdrawnBehavior = false,
        public ?string $behaviorNotes = null,
        public ?AutonomyLevel $autonomyLevel = null,
        public ?string $needsMobilitySupport = null,
        public ?string $needsCommunicationSupport = null,
        public ?string $needsPedagogicalAdaptation = null,
        public ?string $usesAssistiveTechnology = null,
        public bool $hasMedicalReport = false,
        public bool $usesMedication = false,
        public ?string $medicalNotes = null,
    ) {}
}
