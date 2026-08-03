<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\StudentContexts;

use App\Domains\SpecializedEducationalSupport\Domain\Enums\AttentionLevel;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\AutonomyLevel;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\CommunicationType;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\GeneralLevel;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\InteractionLevel;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\MemoryLevel;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\ReasoningLevel;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\SocializationLevel;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidStudentContext;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentContext;

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
