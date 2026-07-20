<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\UI\Controllers;

use App\Domains\InclusiveRadar\Application\Actions\BarrierCategories\CreateBarrierCategoryAction;
use App\Domains\InclusiveRadar\Application\Actions\BarrierCategories\DeleteBarrierCategoryAction;
use App\Domains\InclusiveRadar\Application\Actions\BarrierCategories\UpdateBarrierCategoryAction;
use App\Domains\InclusiveRadar\Application\Data\BarrierCategories\CreateBarrierCategoryData;
use App\Domains\InclusiveRadar\Application\Data\BarrierCategories\ListBarrierCategoriesData;
use App\Domains\InclusiveRadar\Application\Data\BarrierCategories\UpdateBarrierCategoryData;
use App\Domains\InclusiveRadar\Application\Queries\BarrierCategories\ListBarrierCategoriesQuery;
use App\Domains\InclusiveRadar\Application\Queries\BarrierCategories\ShowBarrierCategoryQuery;
use App\Domains\InclusiveRadar\Domain\Models\BarrierCategory;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

final class BarrierCategoryController extends Controller
{
    public function index(ListBarrierCategoriesData $filters, ListBarrierCategoriesQuery $query, Request $request): View
    {
        $categories = $query->execute($filters);

        if ($request->ajax()) {
            return view(
                'pages.inclusive-radar.barrier-categories.partials.table',
                compact('categories'),
            );
        }

        return view(
            'pages.inclusive-radar.barrier-categories.index',
            compact('categories'),
        );
    }

    public function create(): View
    {
        return view('pages.inclusive-radar.barrier-categories.create');
    }

    public function store(CreateBarrierCategoryData $data, CreateBarrierCategoryAction $action): RedirectResponse
    {
        $action->execute($data);

        return redirect()
            ->route('inclusive-radar.barrier-categories.index')
            ->with('success', 'Categoria de barreira cadastrada com sucesso!');
    }

    public function show(BarrierCategory $barrierCategory, ShowBarrierCategoryQuery $query): View
    {
        $barrierCategory = $query->execute($barrierCategory);

        return view(
            'pages.inclusive-radar.barrier-categories.show',
            compact('barrierCategory'),
        );
    }

    public function edit(BarrierCategory $barrierCategory): View
    {
        return view(
            'pages.inclusive-radar.barrier-categories.edit',
            compact('barrierCategory'),
        );
    }

    public function update(
        UpdateBarrierCategoryData $data,
        BarrierCategory $barrierCategory,
        UpdateBarrierCategoryAction $action,
    ): RedirectResponse {
        $action->execute($barrierCategory, $data);

        return redirect()
            ->route('inclusive-radar.barrier-categories.index')
            ->with('success', 'Categoria atualizada com sucesso!');
    }

    /**
     * @throws Throwable
     */
    public function destroy(BarrierCategory $barrierCategory, DeleteBarrierCategoryAction $action): RedirectResponse
    {
        $action->execute($barrierCategory);

        return redirect()
            ->route('inclusive-radar.barrier-categories.index')
            ->with('success', 'Categoria removida com sucesso!');
    }
}
