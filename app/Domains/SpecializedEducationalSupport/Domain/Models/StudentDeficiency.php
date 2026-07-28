<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\Models;

use App\Domains\SpecializedEducationalSupport\Domain\DTOs\StudentDeficiencies\CreateStudentDeficiencyDTO;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\StudentDeficiencies\UpdateStudentDeficiencyDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidStudentDeficiency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

final class StudentDeficiency extends Pivot
{
    use HasFactory;

    protected $table = 'students_deficiencies';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'student_id',
        'deficiency_id',
        'severity',
        'notes',
    ];

    /**
     * @throws InvalidStudentDeficiency
     */
    public static function register(Student $student, Deficiency $deficiency, CreateStudentDeficiencyDTO $data): self
    {
        self::ensurePersisted($student, $deficiency);

        return new self([
            'student_id' => $student->getKey(),
            'deficiency_id' => $deficiency->getKey(),
            'severity' => $data->severity?->value,
            'notes' => self::normalizeNotes($data->notes),
        ]);
    }

    /**
     * @throws InvalidStudentDeficiency
     */
    public function revise(Deficiency $deficiency, UpdateStudentDeficiencyDTO $data): void
    {
        if (! $deficiency->exists) {
            throw new InvalidStudentDeficiency('O vínculo deve ser associado a um perfil de atendimento persistido.');
        }

        $this->fill([
            'deficiency_id' => $deficiency->getKey(),
            'severity' => $data->severity?->value,
            'notes' => self::normalizeNotes($data->notes),
        ]);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function deficiency(): BelongsTo
    {
        return $this->belongsTo(Deficiency::class, 'deficiency_id');
    }

    /**
     * @throws InvalidStudentDeficiency
     */
    private static function ensurePersisted(Student $student, Deficiency $deficiency): void
    {
        if (! $student->exists) {
            throw new InvalidStudentDeficiency('O vínculo deve ser associado a um aluno persistido.');
        }

        if (! $deficiency->exists) {
            throw new InvalidStudentDeficiency('O vínculo deve ser associado a um perfil de atendimento persistido.');
        }
    }

    private static function normalizeNotes(?string $notes): ?string
    {
        $notes = trim((string) $notes);

        return $notes === '' ? null : $notes;
    }
}
