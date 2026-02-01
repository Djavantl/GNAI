<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\AccessibleEducationalMaterialRequest;
use App\Models\InclusiveRadar\AccessibleEducationalMaterial;
use App\Services\InclusiveRadar\AccessibleEducationalMaterialService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AccessibleEducationalMaterialController extends Controller
{
    public function __construct(
        protected AccessibleEducationalMaterialService $service
    ) {}

    public function index(): View
    {
        return view('inclusive-radar.accessible-educational-materials.index', [
            'materials' => $this->service->listAll()
        ]);
    }

    public function create(): View
    {
        return view(
            'inclusive-radar.accessible-educational-materials.create',
            $this->service->getCreateData()
        );
    }

    public function store(AccessibleEducationalMaterialRequest $request): RedirectResponse
    {
        $this->service->store($request->validated());

        return redirect()
            ->route('inclusive-radar.accessible-educational-materials.index')
            ->with('success', 'Material pedagógico acessível criado com sucesso!');
    }

    public function edit(AccessibleEducationalMaterial $material): View
    {
        return view(
            'inclusive-radar.accessible-educational-materials.edit',
            $this->service->getEditData($material)
        );
    }

    public function update(
        AccessibleEducationalMaterialRequest $request,
        AccessibleEducationalMaterial $material
    ): RedirectResponse {
        $this->service->update($material, $request->validated());

        return redirect()
            ->route('inclusive-radar.accessible-educational-materials.index')
            ->with('success', 'Material pedagógico acessível atualizado com sucesso!');
    }

    public function toggleActive(AccessibleEducationalMaterial $material): RedirectResponse
    {
        $this->service->toggleActive($material);

        return redirect()->back()->with('success', 'Status atualizado com sucesso!');
    }

    public function destroy(AccessibleEducationalMaterial $material): RedirectResponse
    {
        $this->service->delete($material);

        return redirect()
            ->route('inclusive-radar.accessible-educational-materials.index')
            ->with('success', 'Material removido com sucesso!');
    }
}
