<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\Teachers;

use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidTeacher;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Teacher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

final readonly class DeleteTeacherAction
{
    /**
     * @throws Throwable
     */
    public function execute(Teacher $teacher): void
    {
        $photo = null;

        DB::transaction(function () use ($teacher, &$photo): void {
            $lockedTeacher = Teacher::query()
                ->with('person')
                ->lockForUpdate()
                ->findOrFail($teacher->getKey());

            if ($lockedTeacher->peiDisciplines()->exists()) {
                throw new InvalidTeacher(
                    'Não é possível excluir este professor pois ele possui registros de acompanhamento em Planos Educacionais Individualizados (PEI).'
                );
            }

            $person = $lockedTeacher->person;
            $photo = $person?->photo;

            $lockedTeacher->user()->delete();
            $lockedTeacher->delete();
            $person?->delete();
        });

        if ($photo !== null) {
            Storage::disk('public')->delete($photo);
        }
    }
}
