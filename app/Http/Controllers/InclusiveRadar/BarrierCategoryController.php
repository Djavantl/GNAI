<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\BarrierCategoryRequest;
use App\Models\InclusiveRadar\BarrierCategory;
use App\Services\InclusiveRadar\BarrierCategoryService;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BarrierCategoryController extends Controller
{
    public function __construct(
        private BarrierCategoryService $service
    ) {}

    public function index(Request $request): View|string
    {
        $categories = BarrierCategory::withCount('barriers')
            ->filterName($request->name)
            ->filterActive($request->is_active)
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return view('pages.inclusive-radar.barrier-categories.partials.table', compact('categories'))->render();
        }

        return view(
            'pages.inclusive-radar.barrier-categories.index',
            compact('categories')
        );
    }

    public function create(): View
    {
        return view('pages.inclusive-radar.barrier-categories.create');
    }

    public function store(BarrierCategoryRequest $request): RedirectResponse
    {
        $this->service->store($request->validated());

        return redirect()
            ->route('inclusive-radar.barrier-categories.index')
            ->with('success', 'Categoria de barreira cadastrada com sucesso!');
    }

    public function show(BarrierCategory $barrierCategory): View
    {
        return view(
            'pages.inclusive-radar.barrier-categories.show',
            compact('barrierCategory')
        );
    }

    public function edit(BarrierCategory $barrierCategory): View
    {
        return view(
            'pages.inclusive-radar.barrier-categories.edit',
            compact('barrierCategory')
        );
    }

    public function update(BarrierCategoryRequest $request, BarrierCategory $barrierCategory): RedirectResponse
    {
        $this->service->update($barrierCategory, $request->validated());

        return redirect()
            ->route('inclusive-radar.barrier-categories.index')
            ->with('success', 'Categoria atualizada com sucesso!');
    }

    public function destroy(BarrierCategory $barrierCategory): RedirectResponse
    {
        $this->service->delete($barrierCategory);

        return redirect()
            ->route('inclusive-radar.barrier-categories.index')
            ->with('success', 'Categoria removida com sucesso!');
    }
}
