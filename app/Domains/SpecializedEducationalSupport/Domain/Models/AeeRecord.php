<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\Models;

use App\Domains\SpecializedEducationalSupport\Domain\DTOs\AeeRecords\CreateAeeRecordDTO;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\AeeRecords\UpdateAeeRecordDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidAeeRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class AeeRecord extends Model
{
    use SoftDeletes;

    protected $table = 'aee_records';

    protected $fillable = [
        'attendance_session_id',
        'duration',
        'activities_performed',
        'strategies_used',
        'resources_used',
        'general_observations',
    ];

    /** @throws InvalidAeeRecord */
    public static function register(Session $session, CreateAeeRecordDTO $data): self
    {
        if (! $session->exists) {
            throw new InvalidAeeRecord('O registro AEE deve ser vinculado a um agendamento persistido.');
        }

        return new self([
            'attendance_session_id' => $session->getKey(),
            ...self::attributesFrom($data),
        ]);
    }

    public function revise(UpdateAeeRecordDTO $data): void
    {
        $this->fill(self::attributesFrom($data));
    }

    public function attendanceSession(): BelongsTo
    {
        return $this->belongsTo(Session::class, 'attendance_session_id')->withTrashed();
    }

    public function studentEvaluations(): HasMany
    {
        return $this->hasMany(AeeStudentEvaluation::class, 'aee_record_id');
    }

    private static function attributesFrom(CreateAeeRecordDTO|UpdateAeeRecordDTO $data): array
    {
        return [
            'duration' => trim($data->duration),
            'activities_performed' => trim($data->activitiesPerformed),
            'strategies_used' => self::nullableText($data->strategiesUsed),
            'resources_used' => self::nullableText($data->resourcesUsed),
            'general_observations' => self::nullableText($data->generalObservations),
        ];
    }

    private static function nullableText(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
