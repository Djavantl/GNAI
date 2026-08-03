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
use Illuminate\View\View;
use Throwable;

final class DeficiencyController
{
    public function index(
        ListDeficienciesData $filters,
        ListDeficienciesQuery $query,
        Request $request,
    ): View {
        $deficiencies = $query->execute($filters);

        if ($request->ajax()) {
            return view(
                'pages.specialized-educational-support.deficiencies.partials.table',
                compact('deficiencies'),
            );
        }

        return view(
            'pages.specialized-educational-support.deficiencies.index',
            compact('deficiencies'),
        );
    }

    public function show(Deficiency $deficiency, ShowDeficiencyQuery $query): View
    {
        $deficiency = $query->execute($deficiency);

        return view(
            'pages.specialized-educational-support.deficiencies.show',
            compact('deficiency'),
        );
    }

    public function create(): View
    {
        return view('pages.specialized-educational-support.deficiencies.create');
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

    public function edit(Deficiency $deficiency): View
    {
        return view(
            'pages.specialized-educational-support.deficiencies.edit',
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
