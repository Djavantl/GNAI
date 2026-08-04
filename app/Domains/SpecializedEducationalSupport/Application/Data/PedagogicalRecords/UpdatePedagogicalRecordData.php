<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Data\PedagogicalRecords;

use App\Domains\SpecializedEducationalSupport\Application\Data\PedagogicalRecords\Concerns\HasPedagogicalRecordValidation;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\PedagogicalFollowUpStatus;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class UpdatePedagogicalRecordData extends Data
{
    use HasPedagogicalRecordValidation;

    public function __construct(
        public string $followUpReason,
        public PedagogicalFollowUpStatus $followUpStatus,
        public string $duration,
        public bool $isPresent,
        public ?string $absenceReason = null,
        public ?string $systematicPedagogicalFollowUpRecord = null,
        public ?string $strategiesAndResourcesAdopted = null,
        public array $failedDisciplineIds = [],
        public array $atRiskDisciplineIds = [],
        public ?string $schoolAttendanceStatus = null,
        public ?string $referralsMade = null,
        public ?string $complementaryObservations = null,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        return self::preparePresence($properties);
    }

    public static function rules(): array
    {
        return self::contentRules();
    }
}
