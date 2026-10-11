<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\UI\Controllers;

use App\Domains\SpecializedEducationalSupport\Application\Actions\Positions\CreatePositionAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\Positions\DeletePositionAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\Positions\TogglePositionActiveAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\Positions\UpdatePositionAction;
use App\Domains\SpecializedEducationalSupport\Application\Data\Positions\CreatePositionData;
use App\Domains\SpecializedEducationalSupport\Application\Data\Positions\ListPositionsData;
use App\Domains\SpecializedEducationalSupport\Application\Data\Positions\UpdatePositionData;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Positions\ListPositionsQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Positions\PositionFormQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Positions\ShowPositionQuery;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Position;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Throwable;

final class PositionController
{
    public function index(ListPositionsData $filters, ListPositionsQuery $query, Request $request): InertiaResponse
    {
        $positions = $query->execute($filters);

        return Inertia::render(
            'SpecializedEducationalSupport/Positions/Index',
            compact('positions'),
        );
    }

    public function show(Position $position, ShowPositionQuery $query): InertiaResponse
    {
        $position = $query->execute($position);

        return Inertia::render(
            'SpecializedEducationalSupport/Positions/Show',
            compact('position'),
        );
    }

    public function create(PositionFormQuery $form): InertiaResponse
    {
        return Inertia::render(
            'SpecializedEducationalSupport/Positions/Create',
            $form->forCreation(),
        );
    }

    /**
     * @throws Throwable
     */
    public function store(CreatePositionData $data, CreatePositionAction $action): RedirectResponse
    {
        $action->execute($data);

        return redirect()
            ->route('specialized-educational-support.positions.index')
            ->with('success', 'Cargo criado com sucesso!');
    }

    public function edit(Position $position, PositionFormQuery $form): InertiaResponse
    {
        return Inertia::render(
            'SpecializedEducationalSupport/Positions/Edit',
            $form->forUpdate($position),
        );
    }

    /**
     * @throws Throwable
     */
    public function update(UpdatePositionData $data, Position $position, UpdatePositionAction $action): RedirectResponse
    {
        $action->execute($position, $data);

        return redirect()
            ->route('specialized-educational-support.positions.index')
            ->with('success', 'Cargo atualizado com sucesso!');
    }

    /**
     * @throws Throwable
     */
    public function toggleActive(Position $position, TogglePositionActiveAction $action): RedirectResponse
    {
        $position = $action->execute($position);
        $message = $position->is_active
            ? 'Cargo ativado com sucesso!'
            : 'Cargo desativado com sucesso!';

        return redirect()
            ->route('specialized-educational-support.positions.index')
            ->with('success', $message);
    }

    /**
     * @throws Throwable
     */
    public function destroy(Position $position, DeletePositionAction $action): RedirectResponse
    {
        $action->execute($position);

        return redirect()
            ->route('specialized-educational-support.positions.index')
            ->with('success', 'Cargo removido!');
    }
}
