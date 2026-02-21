<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\ResourceTypeRequest;
use App\Models\InclusiveRadar\ResourceType;
use App\Services\InclusiveRadar\ResourceTypeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;

class ResourceTypeController extends Controller
{
    public function __construct(
        protected ResourceTypeService $service
    ) {}

    public function index(Request $request)
    {
        $resourceTypes = ResourceType::query()
            ->filterName($request->name)
            ->filterDigital($request->is_digital)
            ->filterActive($request->is_active)
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return view('pages.inclusive-radar.resource-types.partials.table', compact('resourceTypes'))->render();
        }

        return view('pages.inclusive-radar.resource-types.index', compact('resourceTypes'));
    }

    public function create(): View
    {
        return view('pages.inclusive-radar.resource-types.create');
    }

    public function store(ResourceTypeRequest $request): RedirectResponse
    {
        $this->service->store($request->validated());

        return redirect()
            ->route('inclusive-radar.resource-types.index')
            ->with('success', 'Tipo de recurso criado com sucesso!');
    }

    public function show(ResourceType $resourceType): View
    {
        return view(
            'pages.inclusive-radar.resource-types.show',
            compact('resourceType')
        );
    }

    public function edit(ResourceType $resourceType): View
    {
        return view(
            'pages.inclusive-radar.resource-types.edit',
            compact('resourceType')
        );
    }

    public function update(ResourceTypeRequest $request, ResourceType $resourceType): RedirectResponse
    {
        $this->service->update($resourceType, $request->validated());

        return redirect()
            ->route('inclusive-radar.resource-types.index')
            ->with('success', 'Tipo de recurso atualizado com sucesso!');
    }

    public function toggleActive(ResourceType $resourceType): RedirectResponse
    {
        $resourceType = $this->service->toggleActive($resourceType);

        $message = $resourceType->is_active
            ? 'Tipo de recurso ativado com sucesso!'
            : 'Tipo de recurso desativado com sucesso!';

        return redirect()
            ->route('inclusive-radar.resource-types.index')
            ->with('success', $message);
    }

    public function destroy(ResourceType $resourceType): RedirectResponse
    {
        $this->service->delete($resourceType);

        return redirect()
            ->route('inclusive-radar.resource-types.index')
            ->with('success', 'Tipo de recurso removido com sucesso!');
    }
}
