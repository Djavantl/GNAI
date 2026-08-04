<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Data\PedagogicalRecords\Concerns;

use App\Domains\SpecializedEducationalSupport\Domain\Enums\PedagogicalFollowUpStatus;
use Illuminate\Validation\Rule;

trait HasPedagogicalRecordValidation
{
    protected static function contentRules(): array
    {
        return [
            'follow_up_reason' => ['required', 'string', 'max:100000'],
            'follow_up_status' => ['required', Rule::enum(PedagogicalFollowUpStatus::class)],
            'duration' => ['required', 'string', 'max:50'],
            'is_present' => ['required', 'boolean'],
            'absence_reason' => ['required_if:is_present,0', 'nullable', 'string', 'max:100000'],
            'systematic_pedagogical_follow_up_record' => ['required_if:is_present,1', 'nullable', 'string', 'max:100000'],
            'strategies_and_resources_adopted' => ['nullable', 'string', 'max:100000'],
            'failed_discipline_ids' => ['nullable', 'array'],
            'failed_discipline_ids.*' => ['integer', 'distinct', 'exists:disciplines,id'],
            'at_risk_discipline_ids' => ['nullable', 'array'],
            'at_risk_discipline_ids.*' => ['integer', 'distinct', 'exists:disciplines,id'],
            'school_attendance_status' => ['nullable', 'string', 'max:100000'],
            'referrals_made' => ['nullable', 'string', 'max:100000'],
            'complementary_observations' => ['nullable', 'string', 'max:100000'],
        ];
    }

    protected static function preparePresence(array $properties): array
    {
        $properties['is_present'] = filter_var($properties['is_present'] ?? false, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        return $properties;
    }
}
