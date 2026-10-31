<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\Models;

use App\Domains\Audit\Domain\Contracts\Auditable;
use App\Domains\Audit\Domain\ValueObjects\AuditedField;
use App\Domains\Audit\Infrastructure\Eloquent\AuditsChanges;
use App\Domains\Inclusion\Domain\DTOs\StudentDeficiencies\CreateStudentDeficiencyDTO;
use App\Domains\Inclusion\Domain\DTOs\StudentDeficiencies\UpdateStudentDeficiencyDTO;
use App\Domains\Inclusion\Domain\Enums\DeficiencySeverity;
use App\Domains\Inclusion\Domain\Exceptions\InvalidStudentDeficiency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

final class StudentDeficiency extends Pivot implements Auditable
{
    use AuditsChanges;
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

    public static function auditType(): string
    {
        return 'student_deficiency';
    }

    public static function auditLabel(): string
    {
        return 'Deficiência do Aluno';
    }

    public static function auditedFields(): array
    {
        return [
            'student_id' => AuditedField::reference('Aluno', Student::class, 'person.name'),
            'deficiency_id' => AuditedField::reference('Perfil', Deficiency::class),
            'severity' => AuditedField::enum('Severidade', DeficiencySeverity::class),
            'notes' => AuditedField::richText('Observações'),
        ];
    }

    public function auditDescription(): string
    {
        $deficiencyName = $this->deficiency?->name ?? "#{$this->deficiency_id}";
        $studentName = $this->student?->person?->name ?? "#{$this->student_id}";

        return "o perfil de atendimento {$deficiencyName} do aluno {$studentName}";
    }

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
