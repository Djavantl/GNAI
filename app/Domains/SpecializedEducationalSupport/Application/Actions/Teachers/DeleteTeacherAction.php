<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\Teachers;

use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidTeacher;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Teacher;
use App\Domains\SpecializedEducationalSupport\Infrastructure\Storage\TeacherPhotoStorage;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class DeleteTeacherAction
{
    public function __construct(
        private TeacherPhotoStorage $photoStorage,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(Teacher $teacher): void
    {
        $photoPath = DB::transaction(function () use ($teacher): ?string {
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
            $photoPath = $person?->getRawOriginal('photo');

            $lockedTeacher->user()->delete();
            $lockedTeacher->delete();
            $person?->delete();

            return $photoPath;
        });

        $this->photoStorage->delete($photoPath);
    }
}
