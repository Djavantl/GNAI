<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\Models;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\PeiDisciplines\CreatePeiDisciplineDTO;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\PeiDisciplines\UpdatePeiDisciplineDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidPeiDiscipline;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class PeiDiscipline extends Model
{
    use HasFactory;

    protected $table = 'pei_disciplines';

    protected $fillable = [
        'pei_id',
        'creator_id',
        'teacher_id',
        'discipline_id',
        'specific_objectives',
        'content_programmatic',
        'methodologies',
        'evaluations',
        'opinion',
        'complementary_records',
    ];

    /**
     * @throws InvalidPeiDiscipline
     */
    public static function register(
        Pei $pei,
        User $creator,
        Teacher $teacher,
        Discipline $discipline,
        CreatePeiDisciplineDTO $data,
    ): self {
        self::ensurePersisted($pei, $creator, $teacher, $discipline);

        return new self([
            'pei_id' => $pei->getKey(),
            'creator_id' => $creator->getKey(),
            'teacher_id' => $teacher->getKey(),
            'discipline_id' => $discipline->getKey(),
            'specific_objectives' => self::normalizeText($data->specificObjectives),
            'content_programmatic' => self::normalizeText($data->contentProgrammatic),
            'methodologies' => self::normalizeText($data->methodologies),
            'evaluations' => self::normalizeText($data->evaluations),
            'opinion' => self::normalizeNullableText($data->opinion),
            'complementary_records' => self::normalizeNullableText($data->complementaryRecords),
        ]);
    }

    public function revise(
        Teacher $teacher,
        Discipline $discipline,
        UpdatePeiDisciplineDTO $data,
    ): void {
        $this->fill([
            'teacher_id' => $teacher->getKey(),
            'discipline_id' => $discipline->getKey(),
            'specific_objectives' => self::normalizeText($data->specificObjectives),
            'content_programmatic' => self::normalizeText($data->contentProgrammatic),
            'methodologies' => self::normalizeText($data->methodologies),
            'evaluations' => self::normalizeText($data->evaluations),
            'opinion' => self::normalizeNullableText($data->opinion),
            'complementary_records' => self::normalizeNullableText($data->complementaryRecords),
        ]);
    }

    /**
     * @throws InvalidPeiDiscipline
     */
    public function ensureCanBeManagedBy(?int $userId): void
    {
        if ($userId === null || (int) $this->creator_id !== $userId) {
            throw new InvalidPeiDiscipline('Apenas o criador desta adaptação pode editá-la ou removê-la.');
        }
    }

    public function pei(): BelongsTo
    {
        return $this->belongsTo(Pei::class, 'pei_id');
    }

    public function discipline(): BelongsTo
    {
        return $this->belongsTo(Discipline::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function getTeacherDisplayNameAttribute(): string
    {
        return $this->teacher?->person?->name ?? 'Professor s/ Nome';
    }

    /**
     * @throws InvalidPeiDiscipline
     */
    private static function ensurePersisted(Pei $pei, User $creator, Teacher $teacher, Discipline $discipline): void
    {
        if (! $pei->exists || ! $creator->exists || ! $teacher->exists || ! $discipline->exists) {
            throw new InvalidPeiDiscipline(
                'A adaptação do PEI deve ser vinculada a PEI, criador, professor e disciplina persistidos.'
            );
        }
    }

    private static function normalizeText(string $value): string
    {
        return trim($value);
    }

    private static function normalizeNullableText(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
