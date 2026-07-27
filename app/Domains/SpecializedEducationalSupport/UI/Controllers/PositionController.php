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
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

final class PositionController extends Controller
{
    public function index(ListPositionsData $filters, ListPositionsQuery $query, Request $request): View
    {
        $positions = $query->execute($filters);

        if ($request->ajax()) {
            return view(
                'pages.specialized-educational-support.positions.partials.table',
                compact('positions'),
            );
        }

        return view(
            'pages.specialized-educational-support.positions.index',
            compact('positions'),
        );
    }

    public function show(Position $position, ShowPositionQuery $query): View
    {
        $position = $query->execute($position);

        return view(
            'pages.specialized-educational-support.positions.show',
            compact('position'),
        );
    }

    public function create(PositionFormQuery $form): View
    {
        return view(
            'pages.specialized-educational-support.positions.create',
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

    public function edit(Position $position, PositionFormQuery $form): View
    {
        return view(
            'pages.specialized-educational-support.positions.edit',
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
