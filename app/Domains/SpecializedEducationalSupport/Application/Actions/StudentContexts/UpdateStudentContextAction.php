<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\StudentContexts;

use App\Domains\SpecializedEducationalSupport\Application\Data\StudentContexts\UpdateStudentContextData;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\StudentContexts\UpdateStudentContextDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentContext;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class UpdateStudentContextAction
{
    /**
     * @throws Throwable
     */
    public function execute(StudentContext $context, UpdateStudentContextData $data, ?int $professionalId): StudentContext
    {
        return DB::transaction(function () use ($context, $data, $professionalId): StudentContext {
            $student = Student::query()
                ->lockForUpdate()
                ->findOrFail($context->student_id);
            $student->ensureIsActive();

            $lockedContext = StudentContext::query()
                ->lockForUpdate()
                ->findOrFail($context->getKey());
            $lockedContext->ensureCanBeManagedBy($professionalId, 'editar');
            $lockedContext->ensureIsCurrent();
            $lockedContext->revise(new UpdateStudentContextDTO(
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
            ));
            $lockedContext->save();

            return $lockedContext->load(['student.person', 'semester', 'evaluator.person']);
        });
    }
}
