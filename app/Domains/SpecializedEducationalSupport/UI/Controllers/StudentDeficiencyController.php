<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\UI\Controllers;

use App\Domains\SpecializedEducationalSupport\Application\Actions\StudentDeficiencies\CreateStudentDeficiencyAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\StudentDeficiencies\DeleteStudentDeficiencyAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\StudentDeficiencies\UpdateStudentDeficiencyAction;
use App\Domains\SpecializedEducationalSupport\Application\Data\StudentDeficiencies\CreateStudentDeficiencyData;
use App\Domains\SpecializedEducationalSupport\Application\Data\StudentDeficiencies\ListStudentDeficienciesData;
use App\Domains\SpecializedEducationalSupport\Application\Data\StudentDeficiencies\UpdateStudentDeficiencyData;
use App\Domains\SpecializedEducationalSupport\Application\Queries\StudentDeficiencies\ListStudentDeficienciesQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\StudentDeficiencies\ShowStudentDeficiencyQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\StudentDeficiencies\StudentDeficiencyFilterOptionsQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\StudentDeficiencies\StudentDeficiencyFormQuery;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentDeficiency;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Throwable;

final class StudentDeficiencyController
{
    public function index(
        ListStudentDeficienciesData $filters,
        Student $student,
        ListStudentDeficienciesQuery $query,
        StudentDeficiencyFilterOptionsQuery $filterOptions,
        Request $request,
    ): InertiaResponse {
        $deficiencies = $query->execute($student, $filters);

        return Inertia::render('SpecializedEducationalSupport/StudentDeficiencies/Index', [
            'student' => $student->loadMissing('person'),
            'deficiencies' => $deficiencies,
            'filterDeficiencies' => $filterOptions->execute($student),
        ]);
    }

    public function show(
        Student $student,
        StudentDeficiency $student_deficiency,
        ShowStudentDeficiencyQuery $query,
    ): InertiaResponse {
        abort_if((int) $student_deficiency->student_id !== (int) $student->getKey(), 404);

        $deficiency = $query->execute($student_deficiency);

        return Inertia::render(
            'SpecializedEducationalSupport/StudentDeficiencies/Show',
            compact('deficiency', 'student'),
        );
    }

    public function create(Student $student, StudentDeficiencyFormQuery $form): InertiaResponse
    {
        return Inertia::render(
            'SpecializedEducationalSupport/StudentDeficiencies/Create',
            $form->forCreation($student),
        );
    }

    /**
     * @throws Throwable
     */
    public function store(
        CreateStudentDeficiencyData $data,
        Student $student,
        CreateStudentDeficiencyAction $action,
    ): RedirectResponse {
        $action->execute($student, $data);

        return redirect()
            ->route('specialized-educational-support.student-deficiencies.index', $student)
            ->with('success', 'Deficiência vinculada com sucesso.');
    }

    public function edit(
        Student $student,
        StudentDeficiency $student_deficiency,
        StudentDeficiencyFormQuery $form,
    ): InertiaResponse {
        abort_if((int) $student_deficiency->student_id !== (int) $student->getKey(), 404);

        return Inertia::render(
            'SpecializedEducationalSupport/StudentDeficiencies/Edit',
            $form->forUpdate($student, $student_deficiency),
        );
    }

    /**
     * @throws Throwable
     */
    public function update(
        UpdateStudentDeficiencyData $data,
        Student $student,
        StudentDeficiency $student_deficiency,
        UpdateStudentDeficiencyAction $action,
    ): RedirectResponse {
        abort_if((int) $student_deficiency->student_id !== (int) $student->getKey(), 404);

        $action->execute($student_deficiency, $data);

        return redirect()
            ->route('specialized-educational-support.student-deficiencies.index', $student)
            ->with('success', 'Informações da deficiência atualizadas.');
    }

    /**
     * @throws Throwable
     */
    public function destroy(
        Student $student,
        StudentDeficiency $student_deficiency,
        DeleteStudentDeficiencyAction $action,
    ): RedirectResponse {
        abort_if((int) $student_deficiency->student_id !== (int) $student->getKey(), 404);

        $action->execute($student_deficiency);

        return redirect()
            ->route('specialized-educational-support.student-deficiencies.index', $student)
            ->with('success', 'Vínculo removido com sucesso.');
    }
}
