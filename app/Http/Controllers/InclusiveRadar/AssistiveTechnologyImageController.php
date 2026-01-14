<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\AssistiveTechnologyImageRequest;
use App\Models\InclusiveRadar\AssistiveTechnology;
use App\Models\InclusiveRadar\AssistiveTechnologyImage;
use App\Services\InclusiveRadar\AssistiveTechnologyImageService;

class AssistiveTechnologyImageController extends Controller
{
    public function __construct(
        private AssistiveTechnologyImageService $service
    ) {}

    public function store(
        AssistiveTechnologyImageRequest $request,
        AssistiveTechnology $technology
    ) {
        $this->service->store(
            $technology,
            $request->file('image')
        );

        return back()->with('success', 'Imagem adicionada com sucesso.');
    }

    public function destroy(AssistiveTechnologyImage $image)
    {
        $this->service->delete($image);

        return back()->with('success', 'Imagem removida com sucesso.');
    }
}
