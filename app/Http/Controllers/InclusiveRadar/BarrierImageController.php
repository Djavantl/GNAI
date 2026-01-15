<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\BarrierImageRequest;
use App\Models\InclusiveRadar\Barrier;
use App\Models\InclusiveRadar\BarrierImage;
use App\Services\InclusiveRadar\BarrierImageService;

class BarrierImageController extends Controller
{
    public function __construct(
        private BarrierImageService $service
    ) {}

    public function store(
        BarrierImageRequest $request,
        Barrier $barrier
    ) {
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $this->service->store($barrier, $file);
            }
        }

        return back()->with('success', 'Imagem(s) adicionada(s) com sucesso.');
    }

    public function destroy(BarrierImage $image)
    {
        $this->service->delete($image);

        return back()->with('success', 'Imagem removida com sucesso.');
    }
}
