<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\StudentContexts;

use App\Domains\SpecializedEducationalSupport\Application\Data\StudentContexts\CreateStudentContextData;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Semesters\CurrentSemesterQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\StudentContexts\StudentContextEvaluatorQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\StudentContexts\StudentHasContextsQuery;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\StudentContexts\CreateStudentContextDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidStudentContext;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentContext;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CreateStudentContextAction
{
    public function __construct(
        private StudentContextEvaluatorQuery $evaluatorQuery,
        private CurrentSemesterQuery $currentSemesterQuery,
        private StudentHasContextsQuery $studentHasContextsQuery,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(Student $student, CreateStudentContextData $data, ?int $professionalId): StudentContext
    {
        return DB::transaction(function () use ($student, $data, $professionalId): StudentContext {
            $lockedStudent = Student::query()
                ->lockForUpdate()
                ->findOrFail($student->getKey());
            $lockedStudent->ensureIsActive();

            if ($this->studentHasContextsQuery->execute($lockedStudent)) {
                throw new InvalidStudentContext('Este aluno já possui contexto. Crie uma nova versão.');
            }

            $semester = $this->currentSemesterQuery->execute();

            if ($semester === null) {
                throw new InvalidStudentContext('Não existe semestre atual configurado no sistema.');
            }

            $evaluator = $this->evaluatorQuery->execute($professionalId);
            $context = StudentContext::register(
                student: $lockedStudent,
                semester: $semester,
                evaluator: $evaluator,
                data: new CreateStudentContextDTO(
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
            );

            try {
                $context->save();
            } catch (UniqueConstraintViolationException $exception) {
                throw new InvalidStudentContext(
                    'Este aluno já possui contexto. Recarregue a página e tente novamente.',
                    previous: $exception,
                );
            }

            return $context->load(['student.person', 'semester', 'evaluator.person']);
        });
    }
}
