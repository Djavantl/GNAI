<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\StudentContexts;

use App\Domains\SpecializedEducationalSupport\Application\Queries\StudentContexts\StudentContextEvaluatorQuery;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidStudentContext;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentContext;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class RestoreStudentContextVersionAction
{
    public function __construct(
        private StudentContextEvaluatorQuery $evaluatorQuery,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(StudentContext $context, ?int $professionalId): StudentContext
    {
        return DB::transaction(function () use ($context, $professionalId): StudentContext {
            $student = Student::query()
                ->lockForUpdate()
                ->findOrFail($context->student_id);
            $student->ensureIsActive();

            $contexts = StudentContext::query()
                ->where('student_id', $student->getKey())
                ->orderByDesc('version')
                ->lockForUpdate()
                ->get();
            $source = $contexts->first(
                static fn (StudentContext $item): bool => $item->is($context),
            );

            if (! $source instanceof StudentContext) {
                throw new InvalidStudentContext('O contexto selecionado não foi encontrado.');
            }

            if ($source->is_current) {
                throw new InvalidStudentContext('O contexto selecionado já é a versão atual.');
            }

            $evaluator = $this->evaluatorQuery->execute($professionalId);
            $newContext = $source->restoreAsRevision(
                evaluator: $evaluator,
                version: ((int) $contexts->max('version')) + 1,
            );

            $contexts->each(function (StudentContext $item): void {
                if ($item->is_current) {
                    $item->markAsHistorical();
                    $item->save();
                }
            });

            try {
                $newContext->save();
            } catch (UniqueConstraintViolationException $exception) {
                throw new InvalidStudentContext(
                    'Não foi possível restaurar o contexto. Recarregue a página e tente novamente.',
                    previous: $exception,
                );
            }

            return $newContext->load(['student.person', 'semester', 'evaluator.person']);
        });
    }
}
