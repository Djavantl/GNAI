<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\AccessibleEducationalMaterialImageRequest;
use App\Models\InclusiveRadar\AccessibleEducationalMaterial;
use App\Models\InclusiveRadar\AccessibleEducationalMaterialImage;
use App\Services\InclusiveRadar\AccessibleEducationalMaterialImageService;
use Illuminate\Http\RedirectResponse;

class AccessibleEducationalMaterialImageController extends Controller
{
    protected AccessibleEducationalMaterialImageService $service;

    public function __construct(AccessibleEducationalMaterialImageService $service)
    {
        $this->service = $service;
    }

    public function store(AccessibleEducationalMaterialImageRequest $request, AccessibleEducationalMaterial $material): RedirectResponse
    {
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $this->service->store($material, $imageFile);
            }
        }

        return redirect()
            ->route('inclusive-radar.accessible-educational-materials.edit', $material)
            ->with('success', 'Imagens adicionadas com sucesso!');
    }

    public function destroy(AccessibleEducationalMaterialImage $image): RedirectResponse
    {
        $material = $this->service->delete($image);

        return redirect()
            ->route('inclusive-radar.accessible-educational-materials.edit', $material)
            ->with('success', 'Imagem removida com sucesso!');
    }
}
