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
    public function __construct(
        protected AccessibleEducationalMaterialService $accessibleEducationalMaterialService
    ) {}

    public function index(): View
    {
        return view('inclusive-radar.accessible-educational-materials.index', [
            'materials' => $this->accessibleEducationalMaterialService->listAll()
        ]);
    }

    public function create(): View
    {
        return view(
            'inclusive-radar.accessible-educational-materials.create',
            $this->accessibleEducationalMaterialService->getCreateData()
        );
    }

    public function store(AccessibleEducationalMaterialRequest $request): RedirectResponse
    {
        try {
            $this->accessibleEducationalMaterialService->store($request->validated());

            return redirect()
                ->route('inclusive-radar.accessible-educational-materials.index')
                ->with('success', 'Material pedagógico acessível criado com sucesso!');
        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erro ao processar cadastro: ' . $e->getMessage());
        }
    }

    public function edit(AccessibleEducationalMaterial $material): View
    {
        return view(
            'inclusive-radar.accessible-educational-materials.edit',
            $this->accessibleEducationalMaterialService->getEditData($material)
        );
    }

    public function update(
        AccessibleEducationalMaterialRequest $request,
        AccessibleEducationalMaterial $material
    ): RedirectResponse {
        try {
            $this->accessibleEducationalMaterialService->update(
                $material,
                $request->validated()
            );

            return redirect()
                ->route('inclusive-radar.accessible-educational-materials.index')
                ->with('success', 'Material pedagógico acessível atualizado com sucesso!');
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ocorreu um erro inesperado: ' . $e->getMessage());
        }
    }

    public function toggleActive(AccessibleEducationalMaterial $material): RedirectResponse
    {
        $this->accessibleEducationalMaterialService->toggleActive($material);

        return redirect()
            ->back()
            ->with('success', 'Status atualizado com sucesso!');
    }

    public function destroy(AccessibleEducationalMaterial $material): RedirectResponse
    {
        try {
            $this->accessibleEducationalMaterialService->delete($material);

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
                ->with('error', 'Erro ao excluir: ' . $e->getMessage());
        }
    }
}
