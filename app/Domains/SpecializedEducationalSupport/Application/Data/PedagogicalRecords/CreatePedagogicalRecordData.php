<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Data\PedagogicalRecords;

use App\Domains\SpecializedEducationalSupport\Application\Data\PedagogicalRecords\Concerns\HasPedagogicalRecordValidation;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class CreatePedagogicalRecordData extends Data
{
    use HasPedagogicalRecordValidation;

    public function __construct(
        public int $attendanceSessionId,
        public string $duration,
        public bool $isPresent,
        public ?string $absenceReason = null,
        public ?string $plannedPerformedActivities = null,
        public ?string $pedagogicalRecord = null,
        public ?string $resourcesUsed = null,
        public ?string $generalObservations = null,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        return self::preparePresence($properties);
    }

    public static function rules(): array
    {
        return [
            'attendance_session_id' => ['required', 'integer', 'exists:attendance_sessions,id'],
            ...self::contentRules(),
        ];
    }
}
