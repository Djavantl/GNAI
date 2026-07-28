<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\StudentDeficiencies;

use App\Domains\SpecializedEducationalSupport\Application\Data\StudentDeficiencies\UpdateStudentDeficiencyData;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\StudentDeficiencies\UpdateStudentDeficiencyDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidDeficiency;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidStudent;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidStudentDeficiency;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Deficiency;
use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentDeficiency;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class UpdateStudentDeficiencyAction
{
    /**
     * @throws InvalidDeficiency
     * @throws InvalidStudent
     * @throws InvalidStudentDeficiency
     * @throws ModelNotFoundException
     * @throws Throwable
     */
    public function execute(StudentDeficiency $studentDeficiency, UpdateStudentDeficiencyData $data): StudentDeficiency
    {
        return DB::transaction(function () use ($studentDeficiency, $data): StudentDeficiency {
            $lockedStudentDeficiency = StudentDeficiency::query()
                ->with('student')
                ->lockForUpdate()
                ->findOrFail($studentDeficiency->getKey());
            $lockedStudentDeficiency->student->ensureIsActive();

            $lockedDeficiency = Deficiency::query()
                ->lockForUpdate()
                ->findOrFail($data->deficiencyId);
            $lockedDeficiency->ensureIsActive();

            $studentAlreadyHasDeficiency = StudentDeficiency::query()
                ->where('student_id', $lockedStudentDeficiency->student_id)
                ->where('deficiency_id', $lockedDeficiency->getKey())
                ->where('id', '!=', $lockedStudentDeficiency->getKey())
                ->exists();

            if ($studentAlreadyHasDeficiency) {
                throw new InvalidStudentDeficiency('Este aluno já possui o perfil de atendimento selecionado.');
            }

            $studentDeficiencyDTO = new UpdateStudentDeficiencyDTO(
                severity: $data->severity,
                notes: $data->notes,
            );

            $lockedStudentDeficiency->revise(
                deficiency: $lockedDeficiency,
                data: $studentDeficiencyDTO,
            );

            try {
                $lockedStudentDeficiency->save();
            } catch (UniqueConstraintViolationException $exception) {
                throw new InvalidStudentDeficiency(
                    'Este aluno já possui o perfil de atendimento selecionado. Recarregue a página e tente novamente.',
                    previous: $exception,
                );
            }

            return $lockedStudentDeficiency->load(['student.person', 'deficiency']);
        });
    }
}
