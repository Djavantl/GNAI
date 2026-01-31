<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\AccessibleEducationalMaterialRequest;
use App\Models\InclusiveRadar\AccessibleEducationalMaterial;
use App\Services\InclusiveRadar\AccessibleEducationalMaterialService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;
use Exception;

class AccessibleEducationalMaterialController extends Controller
{
    protected AccessibleEducationalMaterialService $service;

    public function __construct(AccessibleEducationalMaterialService $service)
    {
        $this->service = $service;
    }

    public function index(): View
    {
        $materials = $this->service->listAll();

        return view(
            'inclusive-radar.accessible-educational-materials.index',
            compact('materials')
        );
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

    public function update(AccessibleEducationalMaterialRequest $request, AccessibleEducationalMaterial $material): RedirectResponse
    {
        try {
            $this->service->update($material, $request->validated());

            return redirect()
                ->route('inclusive-radar.accessible-educational-materials.index')
                ->with('success', 'Material pedagógico acessível atualizado com sucesso!');

        } catch (ValidationException $e) {
            return back()
                ->withInput()
                ->withErrors($e->validator);
        } catch (Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['quantity' => $e->getMessage()]);
        }
    }

    public function toggleActive(AccessibleEducationalMaterial $material): RedirectResponse
    {
        $this->service->toggleActive($material);

        return redirect()
            ->route('inclusive-radar.accessible-educational-materials.index')
            ->with('success', 'Status do material atualizado com sucesso!');
    }

    public function destroy(AccessibleEducationalMaterial $material): RedirectResponse
    {
        try {
            $this->service->delete($material);

            return redirect()
                ->route('inclusive-radar.accessible-educational-materials.index')
                ->with('success', 'Material removido com sucesso!');

        } catch (ValidationException $e) {
            return redirect()
                ->route('inclusive-radar.accessible-educational-materials.index')
                ->withErrors($e->validator);
        } catch (Exception $e) {
            return redirect()
                ->route('inclusive-radar.accessible-educational-materials.index')
                ->with('error', $e->getMessage());
        }
    }
}
