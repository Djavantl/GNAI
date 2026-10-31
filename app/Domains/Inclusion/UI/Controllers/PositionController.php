<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\UI\Controllers;

use App\Domains\Inclusion\Application\Actions\Positions\CreatePositionAction;
use App\Domains\Inclusion\Application\Actions\Positions\DeletePositionAction;
use App\Domains\Inclusion\Application\Actions\Positions\TogglePositionActiveAction;
use App\Domains\Inclusion\Application\Actions\Positions\UpdatePositionAction;
use App\Domains\Inclusion\Application\Data\Positions\CreatePositionData;
use App\Domains\Inclusion\Application\Data\Positions\ListPositionsData;
use App\Domains\Inclusion\Application\Data\Positions\UpdatePositionData;
use App\Domains\Inclusion\Application\Queries\Positions\ListPositionsQuery;
use App\Domains\Inclusion\Application\Queries\Positions\PositionFormQuery;
use App\Domains\Inclusion\Application\Queries\Positions\ShowPositionQuery;
use App\Domains\Inclusion\Domain\Models\Position;
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
            'Inclusion/Positions/Index',
            [
                'positions' => $positions,
                'filters' => [
                    'name' => $filters->name ?? '',
                    'description' => $filters->description ?? '',
                    'is_active' => $filters->isActive === null ? '' : (string) (int) $filters->isActive,
                ],
                'can' => [
                    'create' => $request->user()?->can('position.create') ?? false,
                    'view' => $request->user()?->can('position.view') ?? false,
                    'update' => $request->user()?->can('position.update') ?? false,
                    'delete' => $request->user()?->can('position.delete') ?? false,
                ],
            ],
        );
    }

    public function show(Position $position, ShowPositionQuery $query, Request $request): InertiaResponse
    {
        $position = $query->execute($position);

        return Inertia::render(
            'Inclusion/Positions/Show',
            [
                'position' => $position,
                'can' => [
                    'update' => $request->user()?->can('position.update') ?? false,
                    'delete' => $request->user()?->can('position.delete') ?? false,
                ],
            ],
        );
    }

    public function create(PositionFormQuery $form): InertiaResponse
    {
        return Inertia::render(
            'Inclusion/Positions/Create',
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
            'Inclusion/Positions/Edit',
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
