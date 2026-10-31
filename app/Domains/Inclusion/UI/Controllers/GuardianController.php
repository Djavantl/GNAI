<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\UI\Controllers;

use App\Domains\Inclusion\Application\Actions\Guardians\CreateGuardianAction;
use App\Domains\Inclusion\Application\Actions\Guardians\DeleteGuardianAction;
use App\Domains\Inclusion\Application\Actions\Guardians\UpdateGuardianAction;
use App\Domains\Inclusion\Application\Data\Guardians\CreateGuardianData;
use App\Domains\Inclusion\Application\Data\Guardians\ListGuardiansData;
use App\Domains\Inclusion\Application\Data\Guardians\UpdateGuardianData;
use App\Domains\Inclusion\Application\Queries\Guardians\GuardianFormQuery;
use App\Domains\Inclusion\Application\Queries\Guardians\ListGuardiansQuery;
use App\Domains\Inclusion\Application\Queries\Guardians\ShowGuardianQuery;
use App\Domains\Inclusion\Domain\Models\Guardian;
use App\Domains\Inclusion\Domain\Models\Student;
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
        $guardians->through(static function (Guardian $guardian): Guardian {
            $guardian->person?->append('photo_url');

            return $guardian;
        });
        $relationships = $form->relationshipOptions();

        return Inertia::render(
            'Inclusion/Guardians/Index',
            [
                ...compact('student', 'guardians', 'relationships'),
                'filters' => [
                    'name' => $filters->name ?? '',
                    'email' => $filters->email ?? '',
                    'relationship' => $filters->relationship?->value ?? '',
                ],
                'can' => [
                    'create' => $request->user()?->can('guardian.create') ?? false,
                    'view' => $request->user()?->can('guardian.view') ?? false,
                    'delete' => $request->user()?->can('guardian.delete') ?? false,
                ],
            ],
        );
    }

    public function show(Guardian $guardian, ShowGuardianQuery $query, Request $request): InertiaResponse
    {
        $guardian = $query->execute($guardian);
        $guardian->person?->append('photo_url');
        $student = $guardian->student;

        return Inertia::render(
            'Inclusion/Guardians/Show',
            [
                ...compact('guardian', 'student'),
                'display' => [
                    'relationshipLabel' => $guardian->relationship?->label(),
                    'genderLabel' => $guardian->person?->gender?->label(),
                    'age' => $guardian->person?->birth_date?->age,
                ],
                'can' => [
                    'update' => $request->user()?->can('guardian.update') ?? false,
                    'delete' => $request->user()?->can('guardian.delete') ?? false,
                ],
            ],
        );
    }

    public function create(Student $student, GuardianFormQuery $form): InertiaResponse
    {
        return Inertia::render(
            'Inclusion/Guardians/Create',
            $form->forCreation($student),
        );
    }

    /**
     * @throws Throwable
     */
    public function store(CreateGuardianData $data, Student $student, CreateGuardianAction $action): RedirectResponse
    {
        $action->execute($student, $data);

        return redirect()
            ->route('specialized-educational-support.students.show', [
                'student' => $student,
                'tab' => 'guardians',
            ])
            ->with('success', 'Responsável vinculado com sucesso.');
    }

    public function edit(Student $student, Guardian $guardian, GuardianFormQuery $form): InertiaResponse
    {
        return Inertia::render(
            'Inclusion/Guardians/Edit',
            $form->forUpdate($student, $guardian),
        );
    }

    /**
     * @throws Throwable
     */
    public function update(UpdateGuardianData $data, Student $student, Guardian $guardian, UpdateGuardianAction $action): RedirectResponse
    {
        $action->execute($student, $guardian, $data);

        return redirect()
            ->route('specialized-educational-support.students.show', [
                'student' => $student,
                'tab' => 'guardians',
            ])
            ->with('success', 'Dados do responsável atualizados com sucesso.');
    }

    /**
     * @throws Throwable
     */
    public function destroy(Student $student, Guardian $guardian, DeleteGuardianAction $action): RedirectResponse
    {
        $action->execute($student, $guardian);

        return redirect()
            ->route('specialized-educational-support.students.show', [
                'student' => $student,
                'tab' => 'guardians',
            ])
            ->with('success', 'Responsável removido.');
    }
}
