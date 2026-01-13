<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssistiveTechnologyImageRequest;
use App\Models\AssistiveTechnology;
use App\Models\AssistiveTechnologyImage;
use App\Services\AssistiveTechnologyImageService;

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
