<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\Models;

use App\Domains\SpecializedEducationalSupport\Domain\DTOs\StudentContexts\CreateStudentContextDTO;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\StudentContexts\UpdateStudentContextDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\ContextEvaluationType;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidStudentContext;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class StudentContext extends Model
{
    protected $table = 'student_contexts';

    protected $fillable = [
        'student_id',
        'semester_id',
        'evaluation_type',
        'is_current',
        'evaluated_by_professional_id',
        'history',
        'specific_educational_needs',
        'learning_level',
        'attention_level',
        'memory_level',
        'reasoning_level',
        'learning_observations',
        'communication_type',
        'interaction_level',
        'socialization_level',
        'shows_aggressive_behavior',
        'shows_withdrawn_behavior',
        'behavior_notes',
        'autonomy_level',
        'needs_mobility_support',
        'needs_communication_support',
        'needs_pedagogical_adaptation',
        'uses_assistive_technology',
        'has_medical_report',
        'uses_medication',
        'medical_notes',
        'knowledge',
        'difficulties',
        'version',
    ];

    protected $casts = [
        'is_current' => 'boolean',
        'shows_aggressive_behavior' => 'boolean',
        'shows_withdrawn_behavior' => 'boolean',
        'has_medical_report' => 'boolean',
        'uses_medication' => 'boolean',
        'version' => 'integer',
    ];

    /**
     * @throws InvalidStudentContext
     */
    public static function register(Student $student, Semester $semester, Professional $evaluator, CreateStudentContextDTO $data): self
    {
        self::ensurePersisted($student, $semester, $evaluator);

        return new self([
            'student_id' => $student->getKey(),
            'semester_id' => $semester->getKey(),
            'evaluation_type' => ContextEvaluationType::INITIAL->value,
            'is_current' => true,
            'evaluated_by_professional_id' => $evaluator->getKey(),
            'version' => 1,
            ...self::attributesFrom($data),
        ]);
    }

    public function revise(UpdateStudentContextDTO $data): void
    {
        $this->fill(self::attributesFrom($data));
    }

    public function createRevision(UpdateStudentContextDTO $data, Professional $evaluator, int $version): self
    {
        $revision = $this->replicate();
        $revision->revise($data);
        $revision->forceFill([
            'evaluation_type' => ContextEvaluationType::PERIODIC_REVIEW->value,
            'is_current' => true,
            'evaluated_by_professional_id' => $evaluator->getKey(),
            'version' => $version,
        ]);

        return $revision;
    }

    public function restoreAsRevision(Professional $evaluator, int $version): self
    {
        $revision = $this->replicate();
        $revision->forceFill([
            'evaluation_type' => ContextEvaluationType::PERIODIC_REVIEW->value,
            'is_current' => true,
            'evaluated_by_professional_id' => $evaluator->getKey(),
            'version' => $version,
        ]);

        return $revision;
    }

    public function markAsHistorical(): void
    {
        $this->is_current = false;
    }

    public function markAsCurrent(): void
    {
        $this->is_current = true;
    }

    /**
     * @throws InvalidStudentContext
     */
    public function ensureIsCurrent(): void
    {
        if (! $this->is_current) {
            throw new InvalidStudentContext('Não é possível editar um contexto que não é atual.');
        }
    }

    /**
     * @throws InvalidStudentContext
     */
    public function ensureCanBeManagedBy(?int $professionalId, string $action = 'alterar'): void
    {
        if (! $this->canBeManagedBy($professionalId)) {
            throw new InvalidStudentContext(
                "Apenas o profissional avaliador deste contexto pode {$action} este registro."
            );
        }
    }

    public function canBeManagedBy(?int $professionalId): bool
    {
        return $professionalId !== null
            && $this->evaluated_by_professional_id !== null
            && (int) $this->evaluated_by_professional_id === $professionalId;
    }

    public function canBeManagedByCurrentUser(): bool
    {
        $professionalId = auth()->user()?->professional_id;

        return $this->canBeManagedBy(
            $professionalId === null ? null : (int) $professionalId,
        );
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(Professional::class, 'evaluated_by_professional_id');
    }

    public function peis(): HasMany
    {
        return $this->hasMany(Pei::class, 'student_context_id');
    }

    /**
     * @throws InvalidStudentContext
     */
    private static function ensurePersisted(Student $student, Semester $semester, Professional $evaluator): void
    {
        if (! $student->exists || ! $semester->exists || ! $evaluator->exists) {
            throw new InvalidStudentContext(
                'O contexto deve ser vinculado a um aluno, semestre e avaliador persistidos.'
            );
        }
    }

    /**
     * @return array<string, string|bool|null>
     */
    private static function attributesFrom(CreateStudentContextDTO|UpdateStudentContextDTO $data): array
    {
        return [
            'history' => trim($data->history),
            'specific_educational_needs' => trim($data->specificEducationalNeeds),
            'learning_level' => $data->learningLevel?->value,
            'attention_level' => $data->attentionLevel?->value,
            'memory_level' => $data->memoryLevel?->value,
            'reasoning_level' => $data->reasoningLevel?->value,
            'learning_observations' => self::normalizeText($data->learningObservations),
            'communication_type' => $data->communicationType?->value,
            'interaction_level' => $data->interactionLevel?->value,
            'socialization_level' => $data->socializationLevel?->value,
            'shows_aggressive_behavior' => $data->showsAggressiveBehavior,
            'shows_withdrawn_behavior' => $data->showsWithdrawnBehavior,
            'behavior_notes' => self::normalizeText($data->behaviorNotes),
            'autonomy_level' => $data->autonomyLevel?->value,
            'needs_mobility_support' => self::normalizeText($data->needsMobilitySupport),
            'needs_communication_support' => self::normalizeText($data->needsCommunicationSupport),
            'needs_pedagogical_adaptation' => self::normalizeText($data->needsPedagogicalAdaptation),
            'uses_assistive_technology' => self::normalizeText($data->usesAssistiveTechnology),
            'has_medical_report' => $data->hasMedicalReport,
            'uses_medication' => $data->usesMedication,
            'medical_notes' => self::normalizeText($data->medicalNotes),
            'knowledge' => trim($data->knowledge),
            'difficulties' => trim($data->difficulties),
        ];
    }

    private static function normalizeText(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
