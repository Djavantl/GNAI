<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\UI\Controllers;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\SpecializedEducationalSupport\Application\Actions\Professionals\CreateProfessionalAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\Professionals\DeleteProfessionalAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\Professionals\UpdateProfessionalAction;
use App\Domains\SpecializedEducationalSupport\Application\Data\Professionals\CreateProfessionalData;
use App\Domains\SpecializedEducationalSupport\Application\Data\Professionals\ListProfessionalsData;
use App\Domains\SpecializedEducationalSupport\Application\Data\Professionals\UpdateProfessionalData;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Professionals\ListProfessionalsQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Professionals\ProfessionalFormQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Professionals\ShowProfessionalQuery;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Professional;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Throwable;

final class ProfessionalController
{
    public function index(ListProfessionalsData $filters, ListProfessionalsQuery $query, ProfessionalFormQuery $form, Request $request): InertiaResponse
    {
        $professionals = $query->execute($filters);

        return Inertia::render(
            'SpecializedEducationalSupport/Professionals/Index',
            ['professionals' => $professionals, ...$form->forIndex()],
        );
    }

    public function show(Professional $professional, ShowProfessionalQuery $query): InertiaResponse
    {
        $professional = $query->execute($professional);

        return Inertia::render(
            'SpecializedEducationalSupport/Professionals/Show',
            compact('professional'),
        );
    }

    public function create(ProfessionalFormQuery $form): InertiaResponse
    {
        return Inertia::render(
            'SpecializedEducationalSupport/Professionals/Create',
            $form->forCreation(),
        );
    }

    /**
     * @throws Throwable
     */
    public function store(CreateProfessionalData $data, CreateProfessionalAction $action, Request $request): RedirectResponse
    {
        $user = $request->user();
        $action->execute($data, $user instanceof User ? $user : null);

        return redirect()
            ->route('specialized-educational-support.professionals.index')
            ->with('success', 'Profissional cadastrado com sucesso. O link para definição da senha foi enviado por e-mail.');
    }

    public function edit(Professional $professional, ProfessionalFormQuery $form): InertiaResponse
    {
        return Inertia::render(
            'SpecializedEducationalSupport/Professionals/Edit',
            $form->forUpdate($professional),
        );
    }

    /**
     * @throws Throwable
     */
    public function update(UpdateProfessionalData $data, Professional $professional, UpdateProfessionalAction $action, Request $request): RedirectResponse
    {
        $user = $request->user();
        $action->execute($professional, $data, $user instanceof User ? $user : null);

        return redirect()
            ->route('specialized-educational-support.professionals.index')
            ->with('success', 'Profissional atualizado com sucesso.');
    }

    /**
     * @throws Throwable
     */
    public function destroy(Professional $professional, DeleteProfessionalAction $action, Request $request): RedirectResponse
    {
        $user = $request->user();
        $action->execute($professional, $user instanceof User ? $user : null);

        return redirect()
            ->route('specialized-educational-support.professionals.index')
            ->with('success', 'Profissional removido com sucesso.');
    }
}
