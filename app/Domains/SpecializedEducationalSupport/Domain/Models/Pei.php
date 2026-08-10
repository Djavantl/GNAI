<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\Models;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\Peis\CreatePeiDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidPei;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Pei extends Model
{
    use HasFactory;

    protected $table = 'peis';

    protected $fillable = [
        'student_id',
        'creator_id',
        'semester_id',
        'course_id',
        'student_context_id',
        'is_finished',
        'version',
        'is_current',
    ];

    protected $casts = [
        'is_finished' => 'boolean',
        'is_current' => 'boolean',
        'version' => 'integer',
    ];

    /**
     * @throws InvalidPei
     */
    public static function register(
        Student $student,
        Semester $semester,
        Course $course,
        StudentContext $studentContext,
        CreatePeiDTO $data,
    ): self {
        self::ensurePersisted($student, $semester, $course, $studentContext);

        return new self([
            'student_id' => $student->getKey(),
            'creator_id' => $data->creatorId,
            'semester_id' => $semester->getKey(),
            'course_id' => $course->getKey(),
            'student_context_id' => $studentContext->getKey(),
            'is_finished' => $data->isFinished,
            'version' => $data->version,
            'is_current' => $data->isCurrent,
        ]);
    }

    /**
     * @throws InvalidPei
     */
    public function ensureCanBeManagedBy(?int $userId): void
    {
        if ($userId === null || (int) $this->creator_id !== $userId) {
            throw new InvalidPei('Acesso negado: apenas o criador do PEI pode gerenciar este registro.');
        }
    }

    /**
     * @throws InvalidPei
     */
    public function ensureCanCreateNewVersion(): void
    {
        if (! $this->is_finished) {
            throw new InvalidPei('O PEI atual ainda está em andamento. Finalize-o antes de criar uma nova versão.');
        }
    }

    /**
     * @throws InvalidPei
     */
    public function ensureCanBeFinished(): void
    {
        $this->loadMissing('peiDisciplines.discipline');

        if ($this->peiDisciplines->isEmpty()) {
            throw new InvalidPei('O PEI precisa ter ao menos uma adaptação cadastrada antes de ser finalizado.');
        }

        $requiredFields = [
            'specific_objectives' => 'objetivos específicos',
            'content_programmatic' => 'conteúdo programático',
            'methodologies' => 'metodologias e estratégias',
            'evaluations' => 'processo de avaliação',
            'opinion' => 'parecer',
        ];

        $incompleteDiscipline = $this->peiDisciplines->first(function (PeiDiscipline $peiDiscipline) use ($requiredFields): bool {
            foreach (array_keys($requiredFields) as $field) {
                if (blank($peiDiscipline->{$field})) {
                    return true;
                }
            }

            return false;
        });

        if ($incompleteDiscipline instanceof PeiDiscipline) {
            $missingFields = collect($requiredFields)
                ->filter(fn (string $label, string $field): bool => blank($incompleteDiscipline->{$field}))
                ->values()
                ->implode(', ');

            $disciplineName = $incompleteDiscipline->discipline->name ?? 'disciplina sem identificação';

            throw new InvalidPei(
                "Não é possível finalizar o PEI. A adaptação da disciplina {$disciplineName} precisa preencher: {$missingFields}."
            );
        }
    }

    public function markAsFinished(): void
    {
        $this->is_finished = true;
        $this->is_current = true;
    }

    public function markAsHistorical(): void
    {
        $this->is_current = false;
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function studentContext(): BelongsTo
    {
        return $this->belongsTo(StudentContext::class);
    }

    public function peiDisciplines(): HasMany
    {
        return $this->hasMany(PeiDiscipline::class, 'pei_id');
    }

    /**
     * @throws InvalidPei
     */
    private static function ensurePersisted(Student $student, Semester $semester, Course $course, StudentContext $studentContext): void
    {
        if (! $student->exists || ! $semester->exists || ! $course->exists || ! $studentContext->exists) {
            throw new InvalidPei(
                'O PEI deve ser vinculado a aluno, semestre, curso e contexto persistidos.'
            );
        }
    }
}
