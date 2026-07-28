<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\StudentDeficiencies;

use App\Domains\SpecializedEducationalSupport\Application\Data\StudentDeficiencies\CreateStudentDeficiencyData;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\StudentDeficiencies\CreateStudentDeficiencyDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidDeficiency;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidStudent;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidStudentDeficiency;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Deficiency;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentDeficiency;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CreateStudentDeficiencyAction
{
    /**
     * @throws InvalidDeficiency
     * @throws InvalidStudent
     * @throws InvalidStudentDeficiency
     * @throws ModelNotFoundException
     * @throws Throwable
     */
    public function execute(Student $student, CreateStudentDeficiencyData $data): StudentDeficiency
    {
        return DB::transaction(function () use ($student, $data): StudentDeficiency {
            $lockedStudent = Student::query()
                ->lockForUpdate()
                ->findOrFail($student->getKey());
            $lockedStudent->ensureIsActive();

            $lockedDeficiency = Deficiency::query()
                ->lockForUpdate()
                ->findOrFail($data->deficiencyId);
            $lockedDeficiency->ensureIsActive();

            $studentAlreadyHasDeficiency = StudentDeficiency::query()
                ->where('student_id', $lockedStudent->getKey())
                ->where('deficiency_id', $lockedDeficiency->getKey())
                ->exists();

            if ($studentAlreadyHasDeficiency) {
                throw new InvalidStudentDeficiency('Este aluno já possui o perfil de atendimento selecionado.');
            }

            $studentDeficiencyDTO = new CreateStudentDeficiencyDTO(
                severity: $data->severity,
                notes: $data->notes,
            );

            $studentDeficiency = StudentDeficiency::register(
                student: $lockedStudent,
                deficiency: $lockedDeficiency,
                data: $studentDeficiencyDTO,
            );

            try {
                $studentDeficiency->save();
            } catch (UniqueConstraintViolationException $exception) {
                throw new InvalidStudentDeficiency(
                    'Este aluno já possui o perfil de atendimento selecionado. Recarregue a página e tente novamente.',
                    previous: $exception,
                );
            }

            return $studentDeficiency->load(['student.person', 'deficiency']);
        });
    }
}
