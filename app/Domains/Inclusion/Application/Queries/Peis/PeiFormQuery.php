<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\Peis;

use App\Domains\Inclusion\Application\Queries\Semesters\CurrentSemesterQuery;
use App\Domains\Inclusion\Domain\Exceptions\InvalidPei;
use App\Domains\Inclusion\Domain\Exceptions\InvalidStudent;
use App\Domains\Inclusion\Domain\Models\Student;

final readonly class PeiFormQuery
{
    public function __construct(
        private CurrentSemesterQuery $currentSemesterQuery,
    ) {}

    /**
     * @return array<string, mixed>
     *
     * @throws InvalidPei
     * @throws InvalidStudent
     */
    public function forCreation(Student $student): array
    {
        $student = $student->loadMissing(['person', 'currentCourse.course', 'currentContext']);
        $student->ensureIsActive();

        $studentCourse = $student->currentCourse;
        if ($studentCourse === null) {
            throw new InvalidPei('Este aluno não possui matrícula vigente');
        }

        $currentContext = $student->currentContext;
        if ($currentContext === null) {
            throw new InvalidPei('Este aluno não possui um contexto atual');
        }

        $semester = $this->currentSemesterQuery->execute();
        if ($semester === null) {
            throw new InvalidPei('O sistema não possui semestre atual configurado');
        }

        return [
            'student' => $student,
            'studentCourse' => $studentCourse,
            'course' => $studentCourse->course,
            'currentContext' => $currentContext,
            'semester' => $semester,
        ];
    }
}
