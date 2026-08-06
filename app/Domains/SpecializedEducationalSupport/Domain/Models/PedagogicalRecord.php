<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\Models;

use App\Domains\SpecializedEducationalSupport\Domain\DTOs\PedagogicalRecords\PedagogicalRecordDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidPedagogicalRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class PedagogicalRecord extends Model
{
    use SoftDeletes;

    protected $table = 'pedagogical_records';

    protected $fillable = [
        'attendance_session_id',
        'follow_up_reason',
        'duration',
        'is_present',
        'absence_reason',
        'systematic_pedagogical_follow_up_record',
        'strategies_and_resources_adopted',
        'referrals_made',
        'complementary_observations',
    ];

    protected $casts = [
        'is_present' => 'boolean',
    ];

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

    public function guardians(): BelongsToMany
    {
        return $this->belongsToMany(
            Guardian::class,
            'pedagogical_record_guardians',
            'pedagogical_record_id',
            'guardian_id',
        )->withTimestamps();
    }

    /** @param list<int> $guardianIds */
    public function syncGuardians(array $guardianIds): void
    {
        $this->guardians()->sync(array_values(array_unique($guardianIds)));
    }

    public function getGuardianNamesAttribute(): string
    {
        return $this->guardians->pluck('person.name')->filter()->join(', ');
    }

    public function getHasGuardiansAttribute(): bool
    {
        return $this->guardians->isNotEmpty();
    }

    private static function attributesFrom(PedagogicalRecordDTO $data): array
    {
        $keepsPedagogicalContent = $data->isPresent || $data->withGuardians;

        return [
            'follow_up_reason' => trim($data->followUpReason),
            'duration' => trim($data->duration),
            'is_present' => $data->isPresent,
            'absence_reason' => $data->isPresent ? null : self::nullableText($data->absenceReason),
            'systematic_pedagogical_follow_up_record' => $keepsPedagogicalContent ? self::nullableText($data->systematicPedagogicalFollowUpRecord) : null,
            'strategies_and_resources_adopted' => $keepsPedagogicalContent ? self::nullableText($data->strategiesAndResourcesAdopted) : null,
            'referrals_made' => $keepsPedagogicalContent ? self::nullableText($data->referralsMade) : null,
            'complementary_observations' => $keepsPedagogicalContent ? self::nullableText($data->complementaryObservations) : null,
        ];
    }

    private static function nullableText(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
