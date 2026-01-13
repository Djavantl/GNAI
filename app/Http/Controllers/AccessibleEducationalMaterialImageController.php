<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccessibleEducationalMaterialImageRequest;
use App\Models\AccessibleEducationalMaterial;
use App\Models\AccessibleEducationalMaterialImage;
use App\Services\AccessibleEducationalMaterialImageService;

class AccessibleEducationalMaterialImageController extends Controller
{
    public function __construct(
        private AccessibleEducationalMaterialImageService $service
    ) {}

    public function store(
        AccessibleEducationalMaterialImageRequest $request,
        AccessibleEducationalMaterial $material
    ) {
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $this->service->store($material, $imageFile);
            }
        }

        return back()->with('success', 'Imagens adicionadas com sucesso.');
    }

    public function destroy(AccessibleEducationalMaterialImage $image)
    {
        $this->service->delete($image);

        return back()->with('success', 'Imagem removida com sucesso.');
    }
}
