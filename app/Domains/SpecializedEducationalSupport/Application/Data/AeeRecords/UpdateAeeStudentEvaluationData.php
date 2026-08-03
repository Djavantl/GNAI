<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Data\AeeRecords;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class UpdateAeeStudentEvaluationData extends Data
{
    public function __construct(
        public int $studentId,
        public bool $isPresent,
        public ?string $absenceReason = null,
        public ?string $studentParticipation = null,
        public ?string $adaptationsMade = null,
        public ?string $developmentEvaluation = null,
        public ?string $progressIndicators = null,
        public ?string $recommendations = null,
        public ?string $nextSessionAdjustments = null,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        $properties['is_present'] = filter_var($properties['is_present'] ?? false, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        return $properties;
    }

    public static function rules(): array
    {
        return [
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'is_present' => ['required', 'boolean'],
            'absence_reason' => ['required_if:is_present,0', 'nullable', 'string', 'max:100000'],
            'student_participation' => ['required_if:is_present,1', 'nullable', 'string', 'max:100000'],
            'development_evaluation' => ['required_if:is_present,1', 'nullable', 'string', 'max:100000'],
            'adaptations_made' => ['nullable', 'string', 'max:100000'],
            'progress_indicators' => ['nullable', 'string', 'max:100000'],
            'recommendations' => ['nullable', 'string', 'max:100000'],
            'next_session_adjustments' => ['nullable', 'string', 'max:100000'],
        ];
    }
}
