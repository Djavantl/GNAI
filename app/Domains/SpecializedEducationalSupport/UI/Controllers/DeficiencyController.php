<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\UI\Controllers;

use App\Domains\SpecializedEducationalSupport\Application\Actions\Deficiencies\CreateDeficiencyAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\Deficiencies\DeleteDeficiencyAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\Deficiencies\ToggleDeficiencyActiveAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\Deficiencies\UpdateDeficiencyAction;
use App\Domains\SpecializedEducationalSupport\Application\Data\Deficiencies\CreateDeficiencyData;
use App\Domains\SpecializedEducationalSupport\Application\Data\Deficiencies\ListDeficienciesData;
use App\Domains\SpecializedEducationalSupport\Application\Data\Deficiencies\UpdateDeficiencyData;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Deficiencies\ListDeficienciesQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Deficiencies\ShowDeficiencyQuery;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Deficiency;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Throwable;

final class DeficiencyController
{
    public function index(
        ListDeficienciesData $filters,
        ListDeficienciesQuery $query,
        Request $request,
    ): InertiaResponse {
        $deficiencies = $query->execute($filters);

        return Inertia::render(
            'SpecializedEducationalSupport/Deficiencies/Index',
            compact('deficiencies'),
        );
    }

    public function show(Deficiency $deficiency, ShowDeficiencyQuery $query): InertiaResponse
    {
        $deficiency = $query->execute($deficiency);

        return Inertia::render(
            'SpecializedEducationalSupport/Deficiencies/Show',
            compact('deficiency'),
        );
    }

    public function create(): InertiaResponse
    {
        return Inertia::render('SpecializedEducationalSupport/Deficiencies/Create');
    }

    public function store(
        CreateDeficiencyData $data,
        CreateDeficiencyAction $action,
    ): RedirectResponse {
        $action->execute($data);

        return redirect()
            ->route('specialized-educational-support.deficiencies.index')
            ->with('success', 'Deficiência criada com sucesso!');
    }

    public function edit(Deficiency $deficiency): InertiaResponse
    {
        return Inertia::render(
            'SpecializedEducationalSupport/Deficiencies/Edit',
            compact('deficiency'),
        );
    }

    public function update(
        UpdateDeficiencyData $data,
        Deficiency $deficiency,
        UpdateDeficiencyAction $action,
    ): RedirectResponse {
        $action->execute($deficiency, $data);

        return redirect()
            ->route('specialized-educational-support.deficiencies.index')
            ->with('success', 'Deficiência atualizada com sucesso!');
    }

    /**
     * @throws Throwable
     */
    public function toggleActive(
        Deficiency $deficiency,
        ToggleDeficiencyActiveAction $action,
    ): RedirectResponse {
        $deficiency = $action->execute($deficiency);
        $message = $deficiency->is_active
            ? 'Deficiência ativada com sucesso!'
            : 'Deficiência desativada com sucesso!';

        return redirect()
            ->route('specialized-educational-support.deficiencies.index')
            ->with('success', $message);
    }

    /**
     * @throws Throwable
     */
    public function destroy(
        Deficiency $deficiency,
        DeleteDeficiencyAction $action,
    ): RedirectResponse {
        $action->execute($deficiency);

        return redirect()
            ->route('specialized-educational-support.deficiencies.index')
            ->with('success', 'Deficiência removida!');
    }
}
