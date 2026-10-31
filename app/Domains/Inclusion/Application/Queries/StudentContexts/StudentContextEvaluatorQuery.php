<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\StudentContexts;

use App\Domains\Inclusion\Domain\Exceptions\InvalidStudentContext;
use App\Domains\Inclusion\Domain\Models\Professional;

final class StudentContextEvaluatorQuery
{
    public function execute(?int $professionalId): Professional
    {
        if ($professionalId === null) {
            throw new InvalidStudentContext(
                'O usuário autenticado não possui profissional vinculado.'
            );
        }

        $professional = Professional::query()
            ->sharedLock()
            ->findOrFail($professionalId);
        $professional->ensureIsActive();

        return $professional;
    }
}
