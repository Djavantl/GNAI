<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\PeiDisciplines;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\SpecializedEducationalSupport\Application\Data\PeiDisciplines\CreatePeiDisciplineData;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\PeiDisciplines\CreatePeiDisciplineDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidDiscipline;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidPei;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidPeiDiscipline;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidStudent;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Discipline;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Pei;
use App\Domains\SpecializedEducationalSupport\Domain\Models\PeiDiscipline;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Teacher;
use App\Models\SpecializedEducationalSupport\TeacherCourseDiscipline;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CreatePeiDisciplineAction
{
    /**
     * @throws InvalidDiscipline
     * @throws InvalidPei
     * @throws InvalidPeiDiscipline
     * @throws InvalidStudent
     * @throws ModelNotFoundException
     * @throws Throwable
     */
    public function execute(Pei $pei, User $creator, CreatePeiDisciplineData $data): PeiDiscipline
    {
        return DB::transaction(function () use ($pei, $creator, $data): PeiDiscipline {
            $lockedPei = Pei::query()
                ->with(['student', 'course'])
                ->lockForUpdate()
                ->findOrFail($pei->getKey());
            $lockedPei->student->ensureIsActive();

            if ($lockedPei->is_finished) {
                throw new InvalidPei('Este PEI já foi finalizado e não permite alterações.');
            }

            $teacher = Teacher::query()
                ->findOrFail($data->teacherId);

            $discipline = Discipline::query()
                ->findOrFail($data->disciplineId);
            $discipline->ensureIsActive();

            $peiAlreadyHasDiscipline = PeiDiscipline::query()
                ->where('pei_id', $lockedPei->getKey())
                ->where('discipline_id', $discipline->getKey())
                ->exists();

            if ($peiAlreadyHasDiscipline) {
                throw new InvalidPeiDiscipline('Já existe uma adaptação para essa disciplina nesse PEI.');
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

            $peiDisciplineDTO = new CreatePeiDisciplineDTO(
                specificObjectives: $data->specificObjectives,
                contentProgrammatic: $data->contentProgrammatic,
                methodologies: $data->methodologies,
                evaluations: $data->evaluations,
                opinion: $data->opinion,
                complementaryRecords: $data->complementaryRecords,
            );

            $peiDiscipline = PeiDiscipline::register(
                pei: $lockedPei,
                creator: $creator,
                teacher: $teacher,
                discipline: $discipline,
                data: $peiDisciplineDTO,
            );

            try {
                $peiDiscipline->save();
            } catch (UniqueConstraintViolationException $exception) {
                throw new InvalidPeiDiscipline(
                    'Já existe uma adaptação para essa disciplina nesse PEI. Recarregue a página e tente novamente.',
                    previous: $exception,
                );
            }

            return $peiDiscipline->load(['pei.student.person', 'teacher.person', 'discipline', 'creator']);
        });
    }
}
