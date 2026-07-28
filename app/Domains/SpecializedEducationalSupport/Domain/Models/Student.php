<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\Models;

use App\Domains\SpecializedEducationalSupport\Domain\DTOs\Students\CreateStudentDTO;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\Students\UpdateStudentDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\StudentStatus;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidStudent;
use App\Models\SpecializedEducationalSupport\Pei;
use App\Models\SpecializedEducationalSupport\StudentContext;
use App\Models\SpecializedEducationalSupport\StudentDocument;
use App\Models\SpecializedEducationalSupport\StudentSessionEvaluation;
use App\Models\Traits\Reportable;
use Carbon\CarbonImmutable;
use Database\Factories\Domains\SpecializedEducationalSupport\StudentFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[UseFactory(StudentFactory::class)]
final class Student extends Model
{
    use HasFactory;
    use Reportable;

    protected $table = 'students';

    protected $fillable = [
        'person_id',
        'registration',
        'entry_date',
        'is_repeater',
        'status',
    ];

    protected $casts = [
        'status' => StudentStatus::class,
        'entry_date' => 'date',
        'is_repeater' => 'boolean',
    ];

    /**
     * @throws InvalidStudent
     */
    public static function register(Person $person, CreateStudentDTO $data): self
    {
        if (! $person->exists) {
            throw new InvalidStudent('O aluno deve ser vinculado a uma pessoa persistida.');
        }

        return new self([
            'person_id' => $person->getKey(),
            ...self::attributesFrom($data),
        ]);
    }

    /**
     * @throws InvalidStudent
     */
    public function revise(UpdateStudentDTO $data): void
    {
        $this->fill(self::attributesFrom($data));
    }

    /**
     * @throws InvalidStudent
     */
    public function ensureIsActive(): void
    {
        if ($this->status !== StudentStatus::ACTIVE) {
            throw new InvalidStudent(
                "O aluno {$this->person->name} não está ativo e não pode realizar esta ação."
            );
        }
    }

    /**
     * @throws InvalidStudent
     */
    public function ensureCanBeDeleted(bool $hasLinkedRecords): void
    {
        if ($hasLinkedRecords) {
            throw new InvalidStudent(
                'Este aluno possui registros acadêmicos ou de atendimento vinculados e não pode ser excluído.'
            );
        }
    }

    public static function getEmbeddedRelations(): array
    {
        return ['person'];
    }

    public static function getReportLabel(): string
    {
        return 'Alunos';
    }

    public static function getReportColumns(): array
    {
        return [
            'person.name',
            'registration',
            'status',
            'entry_date',
            'is_repeater',
            'person.email',
            'person.document',
            'person.birth_date',
            'person.gender',
            'person.phone',
            'person.address',
        ];
    }

    public static function getReportColumnLabels(): array
    {
        return [
            'registration' => 'Matrícula',
            'person.name' => 'Nome do Aluno',
            'entry_date' => 'Data de Ingresso',
            'is_repeater' => 'Repetente',
            'person.email' => 'E-mail',
            'person.document' => 'CPF',
            'person.birth_date' => 'Data de Nascimento',
            'person.gender' => 'Gênero',
            'person.phone' => 'Telefone',
            'person.address' => 'Endereço',
        ];
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function guardians(): HasMany
    {
        return $this->hasMany(Guardian::class, 'student_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(StudentDocument::class, 'student_id');
    }

    public function contexts(): HasMany
    {
        return $this->hasMany(StudentContext::class, 'student_id');
    }

    public function currentContext(): HasOne
    {
        return $this->hasOne(StudentContext::class, 'student_id')
            ->where('is_current', true);
    }

    public function deficiencies(): BelongsToMany
    {
        return $this->belongsToMany(
            Deficiency::class,
            'students_deficiencies',
            'student_id',
            'deficiency_id',
        )
            ->using(StudentDeficiency::class)
            ->withPivot(['severity', 'notes'])
            ->withTimestamps();
    }

    public function peis(): HasMany
    {
        return $this->hasMany(Pei::class, 'student_id');
    }

    public function studentCourses(): HasMany
    {
        return $this->hasMany(StudentCourse::class, 'student_id');
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(
            Course::class,
            'student_courses',
            'student_id',
            'course_id',
        )
            ->using(StudentCourse::class)
            ->withPivot(['academic_year', 'is_current'])
            ->withTimestamps();
    }

    public function currentCourse(): HasOne
    {
        return $this->hasOne(StudentCourse::class, 'student_id')
            ->where('is_current', true)
            ->with('course');
    }

    public function sessions(): BelongsToMany
    {
        return $this->belongsToMany(
            Session::class,
            'attendance_session_student',
            'student_id',
            'attendance_session_id',
        );
    }

    public function sessionEvaluations(): HasMany
    {
        return $this->hasMany(StudentSessionEvaluation::class, 'student_id');
    }

    /**
     * @return array<string, string|bool|null>
     *
     * @throws InvalidStudent
     */
    private static function attributesFrom(CreateStudentDTO|UpdateStudentDTO $data): array
    {
        return [
            'registration' => $data->registration->value(),
            'entry_date' => self::normalizeDate($data->entryDate),
            'is_repeater' => $data->isRepeater,
            'status' => $data->status->value,
        ];
    }

    private static function normalizeDate(?string $date): ?string
    {
        return $date === null
            ? null
            : CarbonImmutable::parse($date)->toDateString();
    }
}
