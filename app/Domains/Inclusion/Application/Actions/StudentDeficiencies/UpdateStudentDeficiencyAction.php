<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\StudentDeficiencies;

use App\Domains\Inclusion\Application\Data\StudentDeficiencies\UpdateStudentDeficiencyData;
use App\Domains\Inclusion\Domain\DTOs\StudentDeficiencies\UpdateStudentDeficiencyDTO;
use App\Domains\Inclusion\Domain\Exceptions\InvalidDeficiency;
use App\Domains\Inclusion\Domain\Exceptions\InvalidStudent;
use App\Domains\Inclusion\Domain\Exceptions\InvalidStudentDeficiency;
use App\Domains\Inclusion\Domain\Models\Deficiency;
use App\Domains\Inclusion\Domain\Models\StudentDeficiency;
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

            $deficiency = Deficiency::query()
                ->findOrFail($data->deficiencyId);
            $deficiency->ensureIsActive();

            $studentAlreadyHasDeficiency = StudentDeficiency::query()
                ->where('student_id', $lockedStudentDeficiency->student_id)
                ->where('deficiency_id', $deficiency->getKey())
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
                deficiency: $deficiency,
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
