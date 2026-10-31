<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\StudentContexts;

use App\Domains\Inclusion\Application\Data\StudentContexts\UpdateStudentContextData;
use App\Domains\Inclusion\Application\Queries\StudentContexts\StudentContextEvaluatorQuery;
use App\Domains\Inclusion\Domain\DTOs\StudentContexts\UpdateStudentContextDTO;
use App\Domains\Inclusion\Domain\Exceptions\InvalidStudentContext;
use App\Domains\Inclusion\Domain\Models\Student;
use App\Domains\Inclusion\Domain\Models\StudentContext;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CreateStudentContextVersionAction
{
    public function __construct(
        private StudentContextEvaluatorQuery $evaluatorQuery,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(Student $student, UpdateStudentContextData $data, ?int $professionalId): StudentContext
    {
        return DB::transaction(function () use ($student, $data, $professionalId): StudentContext {
            $lockedStudent = Student::query()
                ->lockForUpdate()
                ->findOrFail($student->getKey());
            $lockedStudent->ensureIsActive();

            $contexts = StudentContext::query()
                ->where('student_id', $lockedStudent->getKey())
                ->orderByDesc('version')
                ->lockForUpdate()
                ->get();
            $current = $contexts->firstWhere('is_current', true);

            if (! $current instanceof StudentContext) {
                throw new InvalidStudentContext('Não existe contexto atual.');
            }

            $evaluator = $this->evaluatorQuery->execute($professionalId);
            $nextVersion = ((int) $contexts->max('version')) + 1;
            $newContext = $current->createRevision(
                data: new UpdateStudentContextDTO(
                    history: $data->history,
                    specificEducationalNeeds: $data->specificEducationalNeeds,
                    knowledge: $data->knowledge,
                    difficulties: $data->difficulties,
                    learningLevel: $data->learningLevel,
                    attentionLevel: $data->attentionLevel,
                    memoryLevel: $data->memoryLevel,
                    reasoningLevel: $data->reasoningLevel,
                    learningObservations: $data->learningObservations,
                    communicationType: $data->communicationType,
                    interactionLevel: $data->interactionLevel,
                    socializationLevel: $data->socializationLevel,
                    showsAggressiveBehavior: $data->showsAggressiveBehavior,
                    showsWithdrawnBehavior: $data->showsWithdrawnBehavior,
                    behaviorNotes: $data->behaviorNotes,
                    autonomyLevel: $data->autonomyLevel,
                    needsMobilitySupport: $data->needsMobilitySupport,
                    needsCommunicationSupport: $data->needsCommunicationSupport,
                    needsPedagogicalAdaptation: $data->needsPedagogicalAdaptation,
                    usesAssistiveTechnology: $data->usesAssistiveTechnology,
                    hasMedicalReport: $data->hasMedicalReport,
                    usesMedication: $data->usesMedication,
                    medicalNotes: $data->medicalNotes,
                ),
                evaluator: $evaluator,
                version: $nextVersion,
            );

            $contexts->each(function (StudentContext $context): void {
                if ($context->is_current) {
                    $context->markAsHistorical();
                    $context->save();
                }
            });

            try {
                $newContext->save();
            } catch (UniqueConstraintViolationException $exception) {
                throw new InvalidStudentContext(
                    'Não foi possível criar a nova versão. Recarregue a página e tente novamente.',
                    previous: $exception,
                );
            }

            return $newContext->load(['student.person', 'semester', 'evaluator.person']);
        });
    }
}
