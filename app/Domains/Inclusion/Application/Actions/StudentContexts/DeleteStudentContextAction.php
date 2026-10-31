<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\StudentContexts;

use App\Domains\Inclusion\Domain\Exceptions\InvalidStudentContext;
use App\Domains\Inclusion\Domain\Models\Student;
use App\Domains\Inclusion\Domain\Models\StudentContext;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class DeleteStudentContextAction
{
    /**
     * @throws Throwable
     */
    public function execute(StudentContext $context, ?int $professionalId): Student
    {
        return DB::transaction(function () use ($context, $professionalId): Student {
            $student = Student::query()
                ->lockForUpdate()
                ->findOrFail($context->student_id);
            $contexts = StudentContext::query()
                ->where('student_id', $student->getKey())
                ->orderByDesc('version')
                ->lockForUpdate()
                ->get();
            $lockedContext = $contexts->first(
                static fn (StudentContext $item): bool => $item->is($context),
            );

            if (! $lockedContext instanceof StudentContext) {
                throw new InvalidStudentContext('O contexto selecionado não foi encontrado.');
            }

            $lockedContext->ensureCanBeManagedBy($professionalId, 'excluir');
            $wasCurrent = $lockedContext->is_current;
            $lockedContext->delete();

            if ($wasCurrent) {
                $previous = $contexts->first(
                    static fn (StudentContext $item): bool => ! $item->is($lockedContext),
                );

                if ($previous instanceof StudentContext) {
                    $previous->markAsCurrent();
                    $previous->save();
                }
            }

            return $student->load('person');
        });
    }
}
