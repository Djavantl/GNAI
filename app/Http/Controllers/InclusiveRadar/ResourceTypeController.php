<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\ResourceTypeRequest;
use App\Models\InclusiveRadar\ResourceType;
use App\Services\InclusiveRadar\ResourceTypeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ResourceTypeController extends Controller
{
    public function __construct(private ResourceTypeService $service) {}

    public function index(): View
    {
        $types = ResourceType::orderBy('name')->get();
        return view('inclusive-radar.resource-types.index', compact('types'));
    }

    public function create(): View
    {
        return view('inclusive-radar.resource-types.create');
    }

    public function store(ResourceTypeRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()
            ->route('inclusive-radar.resource-types.index')
            ->with('success', 'Tipo de recurso criado com sucesso.');
    }

    public function edit(ResourceType $resource_type): View
    {
        return view('inclusive-radar.resource-types.edit', ['type' => $resource_type]);
    }

    public function update(ResourceTypeRequest $request, ResourceType $resource_type): RedirectResponse
    {
        $this->service->update($resource_type, $request->validated());

        return redirect()
            ->route('inclusive-radar.resource-types.index')
            ->with('success', 'Tipo de recurso atualizado com sucesso.');
    }

    public function toggle(ResourceType $resource_type): RedirectResponse
    {
        $this->service->toggleActive($resource_type);

        return redirect()
            ->back()
            ->with('success', 'Status de ativo alterado com sucesso.');
    }

    public function destroy(ResourceType $resource_type): RedirectResponse
    {
        $this->service->delete($resource_type);

        return redirect()
            ->route('inclusive-radar.resource-types.index')
            ->with('success', 'Tipo de recurso excluído com sucesso.');
    }
}
