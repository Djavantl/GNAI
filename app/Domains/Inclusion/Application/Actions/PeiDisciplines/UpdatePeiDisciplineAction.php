<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\PeiDisciplines;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\Inclusion\Application\Data\PeiDisciplines\UpdatePeiDisciplineData;
use App\Domains\Inclusion\Domain\DTOs\PeiDisciplines\UpdatePeiDisciplineDTO;
use App\Domains\Inclusion\Domain\Exceptions\InvalidDiscipline;
use App\Domains\Inclusion\Domain\Exceptions\InvalidPei;
use App\Domains\Inclusion\Domain\Exceptions\InvalidPeiDiscipline;
use App\Domains\Inclusion\Domain\Exceptions\InvalidStudent;
use App\Domains\Inclusion\Domain\Models\Discipline;
use App\Domains\Inclusion\Domain\Models\Pei;
use App\Domains\Inclusion\Domain\Models\PeiDiscipline;
use App\Domains\Inclusion\Domain\Models\Teacher;
use App\Domains\Inclusion\Domain\Models\TeacherCourseDiscipline;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class UpdatePeiDisciplineAction
{
    /**
     * @throws InvalidDiscipline
     * @throws InvalidPei
     * @throws InvalidPeiDiscipline
     * @throws InvalidStudent
     * @throws ModelNotFoundException
     * @throws Throwable
     */
    public function execute(PeiDiscipline $peiDiscipline, User $user, UpdatePeiDisciplineData $data): PeiDiscipline
    {
        return DB::transaction(function () use ($peiDiscipline, $user, $data): PeiDiscipline {
            $lockedPei = Pei::query()
                ->with(['student', 'course'])
                ->lockForUpdate()
                ->findOrFail($peiDiscipline->pei_id);
            $lockedPei->student->ensureIsActive();

            $lockedPeiDiscipline = PeiDiscipline::query()
                ->lockForUpdate()
                ->where('pei_id', $lockedPei->getKey())
                ->findOrFail($peiDiscipline->getKey());

            $lockedPeiDiscipline->ensureCanBeManagedBy((int) $user->getKey());

            if ($lockedPei->is_finished) {
                throw new InvalidPei('Este PEI já foi finalizado e não permite alterações.');
            }

            $teacher = Teacher::query()
                ->findOrFail($data->teacherId);

            $discipline = Discipline::query()
                ->findOrFail($data->disciplineId);
            $discipline->ensureIsActive();

            if ((int) $lockedPeiDiscipline->discipline_id !== (int) $discipline->getKey()) {
                $peiAlreadyHasDiscipline = PeiDiscipline::query()
                    ->where('pei_id', $lockedPeiDiscipline->pei_id)
                    ->where('discipline_id', $discipline->getKey())
                    ->exists();

                if ($peiAlreadyHasDiscipline) {
                    throw new InvalidPeiDiscipline('Já existe uma adaptação para essa disciplina nesse PEI.');
                }
            }

            $disciplineBelongsToCourse = $lockedPei->course
                ->disciplines()
                ->where('disciplines.id', $discipline->getKey())
                ->exists();

            if (! $disciplineBelongsToCourse) {
                throw new InvalidPeiDiscipline('A disciplina selecionada não faz parte do curso do aluno.');
            }

            $teacherBelongsToCourseAndDiscipline = TeacherCourseDiscipline::query()
                ->where('teacher_id', $teacher->getKey())
                ->where('course_id', $lockedPei->course_id)
                ->where('discipline_id', $discipline->getKey())
                ->exists();

            if (! $teacherBelongsToCourseAndDiscipline) {
                throw new InvalidPeiDiscipline('O professor selecionado não está vinculado à disciplina neste curso.');
            }

            $peiDisciplineDTO = new UpdatePeiDisciplineDTO(
                specificObjectives: $data->specificObjectives,
                contentProgrammatic: $data->contentProgrammatic,
                methodologies: $data->methodologies,
                evaluations: $data->evaluations,
                opinion: $data->opinion,
                complementaryRecords: $data->complementaryRecords,
            );

            $lockedPeiDiscipline->revise(
                teacher: $teacher,
                discipline: $discipline,
                data: $peiDisciplineDTO,
            );

            try {
                $lockedPeiDiscipline->save();
            } catch (UniqueConstraintViolationException $exception) {
                throw new InvalidPeiDiscipline(
                    'Já existe uma adaptação para essa disciplina nesse PEI. Recarregue a página e tente novamente.',
                    previous: $exception,
                );
            }

            return $lockedPeiDiscipline->load(['pei.student.person', 'teacher.person', 'discipline', 'creator']);
        });
    }
}
