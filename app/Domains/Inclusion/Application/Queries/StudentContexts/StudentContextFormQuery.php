<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\StudentContexts;

use App\Domains\Inclusion\Domain\Enums\AttentionLevel;
use App\Domains\Inclusion\Domain\Enums\AutonomyLevel;
use App\Domains\Inclusion\Domain\Enums\CommunicationType;
use App\Domains\Inclusion\Domain\Enums\DeficiencySeverity;
use App\Domains\Inclusion\Domain\Enums\GeneralLevel;
use App\Domains\Inclusion\Domain\Enums\InteractionLevel;
use App\Domains\Inclusion\Domain\Enums\MemoryLevel;
use App\Domains\Inclusion\Domain\Enums\ReasoningLevel;
use App\Domains\Inclusion\Domain\Enums\SocializationLevel;
use App\Domains\Inclusion\Domain\Exceptions\InvalidStudentContext;
use App\Domains\Inclusion\Domain\Models\Student;
use App\Domains\Inclusion\Domain\Models\StudentContext;

final class StudentContextFormQuery
{
    public function __construct(
        private readonly StudentHasContextsQuery $studentHasContextsQuery,
    ) {}

    public function forCreation(Student $student): array
    {
        $student->ensureIsActive();

        if ($this->studentHasContextsQuery->execute($student)) {
            throw new InvalidStudentContext(
                'Este aluno já possui contexto. Use "Nova Versão".'
            );
        }

        return [
            'student' => $student->loadMissing(['person', 'deficiencies']),
            'deficiencies' => $student->deficiencies,
            'studentContextOptions' => $this->options(),
            'studentProfile' => $this->studentProfile($student),
        ];
    }

    public function forUpdate(StudentContext $context, ?int $professionalId): array
    {
        $context->loadMissing(['student.person', 'student.deficiencies']);
        $context->student->ensureIsActive();
        $context->ensureCanBeManagedBy($professionalId, 'editar');
        $context->ensureIsCurrent();

        return [
            'studentContext' => $context,
            'student' => $context->student,
            'deficiencies' => $context->student->deficiencies,
            'studentContextOptions' => $this->options(),
            'studentProfile' => $this->studentProfile($context->student),
        ];
    }

    public function forNewVersion(Student $student): array
    {
        $student->ensureIsActive();
        $current = StudentContext::query()
            ->where('student_id', $student->getKey())
            ->where('is_current', true)
            ->with(['student.person', 'student.deficiencies'])
            ->first();

        if (! $current instanceof StudentContext) {
            throw new InvalidStudentContext('Não existe contexto atual.');
        }

        $nextVersion = ((int) StudentContext::query()
            ->where('student_id', $student->getKey())
            ->max('version')) + 1;
        $preview = $current->replicate();
        $preview->version = $nextVersion;

        return [
            'studentContext' => $preview,
            'student' => $student->loadMissing(['person', 'deficiencies']),
            'deficiencies' => $student->deficiencies,
            'studentContextOptions' => $this->options(),
        ];
    }

    /**
     * Read-only presentation data shared by the context details page.
     *
     * @return array{studentContextOptions: array<string, array<string, string>>, studentProfile: array<string, mixed>}
     */
    public function forDisplay(Student $student): array
    {
        $student->loadMissing(['person', 'deficiencies']);

        return [
            'studentContextOptions' => $this->options(),
            'studentProfile' => $this->studentProfile($student),
        ];
    }

    /**
     * Presentation values the legacy form read through accessors and enum methods.
     *
     * @return array<string, mixed>
     */
    private function studentProfile(Student $student): array
    {
        return [
            'name' => $student->person->name,
            'registration' => $student->registration,
            'photo_url' => $student->person->photo_url,
            'status_label' => $student->status->label(),
            'status_color' => $student->status->color(),
            'deficiencies' => $student->deficiencies->map(fn ($deficiency): array => [
                'id' => (int) $deficiency->getKey(),
                'name' => $deficiency->name,
                'severity' => $deficiency->pivot->severity,
                'severity_label' => DeficiencySeverity::tryFrom((string) $deficiency->pivot->severity)?->label(),
            ])->values()->all(),
        ];
    }

    private function options(): array
    {
        return [
            'learningLevels' => collect(GeneralLevel::cases())
                ->mapWithKeys(fn (GeneralLevel $level): array => [$level->value => $level->label()])
                ->all(),
            'attentionLevels' => collect(AttentionLevel::cases())
                ->mapWithKeys(fn (AttentionLevel $level): array => [$level->value => $level->label()])
                ->all(),
            'memoryLevels' => collect(MemoryLevel::cases())
                ->mapWithKeys(fn (MemoryLevel $level): array => [$level->value => $level->label()])
                ->all(),
            'reasoningLevels' => collect(ReasoningLevel::cases())
                ->mapWithKeys(fn (ReasoningLevel $level): array => [$level->value => $level->label()])
                ->all(),
            'communicationTypes' => collect(CommunicationType::cases())
                ->mapWithKeys(fn (CommunicationType $type): array => [$type->value => $type->label()])
                ->all(),
            'interactionLevels' => collect(InteractionLevel::cases())
                ->mapWithKeys(fn (InteractionLevel $level): array => [$level->value => $level->label()])
                ->all(),
            'socializationLevels' => collect(SocializationLevel::cases())
                ->mapWithKeys(fn (SocializationLevel $level): array => [$level->value => $level->label()])
                ->all(),
            'autonomyLevels' => collect(AutonomyLevel::cases())
                ->mapWithKeys(fn (AutonomyLevel $level): array => [$level->value => $level->label()])
                ->all(),
        ];
    }
}
