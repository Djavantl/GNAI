<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\UI\Controllers;

use App\Domains\SpecializedEducationalSupport\Application\Actions\Guardians\CreateGuardianAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\Guardians\DeleteGuardianAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\Guardians\UpdateGuardianAction;
use App\Domains\SpecializedEducationalSupport\Application\Data\Guardians\CreateGuardianData;
use App\Domains\SpecializedEducationalSupport\Application\Data\Guardians\ListGuardiansData;
use App\Domains\SpecializedEducationalSupport\Application\Data\Guardians\UpdateGuardianData;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Guardians\GuardianFormQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Guardians\ListGuardiansQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Guardians\ShowGuardianQuery;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Guardian;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Throwable;

final class GuardianController
{
    public function index(Student $student, ListGuardiansData $filters, ListGuardiansQuery $query, GuardianFormQuery $form, Request $request): InertiaResponse
    {
        $student->loadMissing('person');
        $guardians = $query->execute($student, $filters);
        $relationships = $form->relationshipOptions();

        return Inertia::render(
            'SpecializedEducationalSupport/Guardians/Index',
            compact('student', 'guardians', 'relationships'),
        );
    }

    public function show(Guardian $guardian, ShowGuardianQuery $query): InertiaResponse
    {
        $guardian = $query->execute($guardian);
        $student = $guardian->student;

        return Inertia::render(
            'SpecializedEducationalSupport/Guardians/Show',
            compact('guardian', 'student'),
        );
    }

    public function create(Student $student, GuardianFormQuery $form): InertiaResponse
    {
        return Inertia::render(
            'SpecializedEducationalSupport/Guardians/Create',
            $form->forCreation($student),
        );
    }

    /**
     * @throws Throwable
     */
    public function store(CreateGuardianData $data, Student $student, CreateGuardianAction $action): RedirectResponse
    {
        $guardian = $action->execute($student, $data);

        return redirect()
            ->route('specialized-educational-support.guardians.show', $guardian)
            ->with('success', 'Responsável vinculado com sucesso.');
    }

    public function edit(Student $student, Guardian $guardian, GuardianFormQuery $form): InertiaResponse
    {
        return Inertia::render(
            'SpecializedEducationalSupport/Guardians/Edit',
            $form->forUpdate($student, $guardian),
        );
    }

    /**
     * @throws Throwable
     */
    public function update(UpdateGuardianData $data, Student $student, Guardian $guardian, UpdateGuardianAction $action): RedirectResponse
    {
        $guardian = $action->execute($student, $guardian, $data);

        return redirect()
            ->route('specialized-educational-support.guardians.show', $guardian)
            ->with('success', 'Dados do responsável atualizados com sucesso.');
    }

    /**
     * @throws Throwable
     */
    public function destroy(Student $student, Guardian $guardian, DeleteGuardianAction $action): RedirectResponse
    {
        $action->execute($student, $guardian);

        return redirect()
            ->route('specialized-educational-support.guardians.index', $student)
            ->with('success', 'Responsável removido.');
    }
}
