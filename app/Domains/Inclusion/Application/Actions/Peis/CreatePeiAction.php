<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\Peis;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\Inclusion\Application\Queries\Semesters\CurrentSemesterQuery;
use App\Domains\Inclusion\Domain\DTOs\Peis\CreatePeiDTO;
use App\Domains\Inclusion\Domain\Exceptions\InvalidPei;
use App\Domains\Inclusion\Domain\Models\Pei;
use App\Domains\Inclusion\Domain\Models\Student;
use App\Domains\Inclusion\Domain\Models\StudentContext;
use App\Domains\Inclusion\Domain\Models\StudentCourse;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CreatePeiAction
{
    public function __construct(
        private CurrentSemesterQuery $currentSemesterQuery,
    ) {}

    /**
     * @throws InvalidPei
     * @throws Throwable
     */
    public function execute(Student $student, User $creator): Pei
    {
        return DB::transaction(function () use ($student, $creator): Pei {
            $lockedStudent = Student::query()
                ->lockForUpdate()
                ->findOrFail($student->getKey());
            $lockedStudent->ensureIsActive();

            $currentPei = Pei::query()
                ->with('course')
                ->where('student_id', $lockedStudent->getKey())
                ->where('is_current', true)
                ->lockForUpdate()
                ->first();

            if ($currentPei instanceof Pei) {
                $currentPei->ensureCanCreateNewVersion();
                $course = $currentPei->course;
                $version = ((int) $currentPei->version) + 1;
            } else {
                $studentCourse = StudentCourse::query()
                    ->with('course')
                    ->where('student_id', $lockedStudent->getKey())
                    ->where('is_current', true)
                    ->first();

                if (! $studentCourse instanceof StudentCourse) {
                    throw new InvalidPei('Este aluno não possui matrícula vigente');
                }

                $course = $studentCourse->course;
                $version = 1;
            }

            $semester = $this->currentSemesterQuery->execute();
            if ($semester === null) {
                throw new InvalidPei('O sistema não possui semestre atual cadastrado');
            }

            $currentContext = StudentContext::query()
                ->where('student_id', $lockedStudent->getKey())
                ->where('is_current', true)
                ->first();

            if (! $currentContext instanceof StudentContext) {
                throw new InvalidPei('Este aluno não possui um Contexto atual definido.');
            }

            if ($currentPei instanceof Pei) {
                $currentPei->markAsHistorical();
                $currentPei->save();
            }

            $peiDTO = new CreatePeiDTO(
                creatorId: (int) $creator->getKey(),
                version: $version,
            );

            $pei = Pei::register(
                student: $lockedStudent,
                semester: $semester,
                course: $course,
                studentContext: $currentContext,
                data: $peiDTO,
            );
            $pei->save();

            return $pei->load(['student.person', 'semester', 'course', 'studentContext']);
        });
    }
}
