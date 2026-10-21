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
use App\Shared\Infrastructure\Security\RichTextSanitizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Throwable;

final class BarrierCategoryController
{
    public function index(ListBarrierCategoriesData $filters, ListBarrierCategoriesQuery $query, Request $request): InertiaResponse
    {
        $categories = $query->execute($filters);

        return Inertia::render(
            'InclusiveRadar/BarrierCategories/Index',
            [
                'categories' => $categories,
                'filters' => [
                    'name' => $filters->name ?? '',
                    'is_active' => $filters->isActive === null ? '' : ($filters->isActive ? '1' : '0'),
                ],
                'can' => [
                    'create' => $request->user()->can('barrier-category.create'),
                    'show' => $request->user()->can('barrier-category.show'),
                    'destroy' => $request->user()->can('barrier-category.destroy'),
                ],
            ],
        );
    }

    public function create(Request $request): InertiaResponse
    {
        return Inertia::render('InclusiveRadar/BarrierCategories/Create', [
            'can' => ['store' => $request->user()->can('barrier-category.store')],
        ]);
    }

    public function store(CreateBarrierCategoryData $data, CreateBarrierCategoryAction $action): RedirectResponse
    {
        $action->execute($data);

        return redirect()
            ->route('inclusive-radar.barrier-categories.index')
            ->with('success', 'Categoria de barreira cadastrada com sucesso!');
    }

    public function show(BarrierCategory $barrierCategory, ShowBarrierCategoryQuery $query, Request $request): InertiaResponse
    {
        $barrierCategory = $query->execute($barrierCategory);

        return Inertia::render(
            'InclusiveRadar/BarrierCategories/Show',
            [
                'barrierCategory' => $barrierCategory,
                'display' => [
                    'descriptionHtml' => RichTextSanitizer::sanitize($barrierCategory->description ?: '— Não informada —'),
                ],
                'can' => [
                    'edit' => $request->user()->can('barrier-category.edit'),
                    'destroy' => $request->user()->can('barrier-category.destroy'),
                ],
            ],
        );
    }

    public function edit(BarrierCategory $barrierCategory, Request $request): InertiaResponse
    {
        return Inertia::render(
            'InclusiveRadar/BarrierCategories/Edit',
            [
                'barrierCategory' => $barrierCategory,
                'can' => ['update' => $request->user()->can('barrier-category.update')],
            ],
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
