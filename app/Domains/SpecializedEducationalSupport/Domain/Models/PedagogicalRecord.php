<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\Models;

use App\Domains\SpecializedEducationalSupport\Domain\DTOs\PedagogicalRecords\PedagogicalRecordDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidPedagogicalRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

final class PedagogicalRecord extends Model
{
    use SoftDeletes;

    protected $table = 'pedagogical_records';

    protected $fillable = [
        'attendance_session_id',
        'duration',
        'is_present',
        'absence_reason',
        'planned_performed_activities',
        'pedagogical_record',
        'resources_used',
        'general_observations',
    ];

    protected $casts = ['is_present' => 'boolean'];

    /** @throws InvalidPedagogicalRecord */
    public static function register(Session $session, PedagogicalRecordDTO $data): self
    {
        if (! $session->exists) {
            throw new InvalidPedagogicalRecord('O registro pedagógico deve ser vinculado a um agendamento persistido.');
        }

        return new self([
            'attendance_session_id' => $session->getKey(),
            ...self::attributesFrom($data),
        ]);
    }

    public function revise(PedagogicalRecordDTO $data): void
    {
        $this->fill(self::attributesFrom($data));
    }

    public function attendanceSession(): BelongsTo
    {
        return $this->belongsTo(Session::class, 'attendance_session_id')->withTrashed();
    }

    private static function attributesFrom(PedagogicalRecordDTO $data): array
    {
        return [
            'duration' => trim($data->duration),
            'is_present' => $data->isPresent,
            'absence_reason' => $data->isPresent ? null : self::nullableText($data->absenceReason),
            'planned_performed_activities' => $data->isPresent ? self::nullableText($data->plannedPerformedActivities) : null,
            'pedagogical_record' => $data->isPresent ? self::nullableText($data->pedagogicalRecord) : null,
            'resources_used' => $data->isPresent ? self::nullableText($data->resourcesUsed) : null,
            'general_observations' => $data->isPresent ? self::nullableText($data->generalObservations) : null,
        ];
    }

    private static function nullableText(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
