<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\BarrierCategoryRequest;
use App\Models\InclusiveRadar\BarrierCategory;
use App\Services\InclusiveRadar\BarrierCategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BarrierCategoryController extends Controller
{
    public function __construct(protected BarrierCategoryService $service) {}

    public function index(): View
    {
        $categories = $this->service->listAll();
        return view('inclusive-radar.barrier-categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('inclusive-radar.barrier-categories.create');
    }

    public function store(BarrierCategoryRequest $request): RedirectResponse
    {
        $this->service->store($request->validated());

        return redirect()
            ->route('inclusive-radar.barrier-categories.index')
            ->with('success', 'Categoria de barreira cadastrada com sucesso!');
    }

    public function edit(BarrierCategory $barrierCategory): View
    {
        return view('inclusive-radar.barrier-categories.edit', compact('barrierCategory'));
    }

    public function update(BarrierCategoryRequest $request, BarrierCategory $barrierCategory): RedirectResponse
    {
        $this->service->update($barrierCategory, $request->validated());

        return redirect()
            ->route('inclusive-radar.barrier-categories.index')
            ->with('success', 'Categoria atualizada com sucesso!');
    }

    public function toggleActive(BarrierCategory $barrierCategory): RedirectResponse
    {
        $this->service->toggleActive($barrierCategory);

        return redirect()
            ->back()
            ->with('success', 'Status da categoria atualizado com sucesso!');
    }

    public function destroy(BarrierCategory $barrierCategory): RedirectResponse
    {
        try {
            $this->service->delete($barrierCategory);

            return redirect()
                ->route('inclusive-radar.barrier-categories.index')
                ->with('success', 'Categoria removida com sucesso!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['Não foi possível excluir esta categoria pois ela pode estar vinculada a barreiras existentes.']);
        }
    }
}
