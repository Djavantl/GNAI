<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\AssistiveTechnologyImageRequest;
use App\Models\InclusiveRadar\AssistiveTechnology;
use App\Models\InclusiveRadar\AssistiveTechnologyImage;
use App\Services\InclusiveRadar\AssistiveTechnologyImageService;
use Illuminate\Http\RedirectResponse;

class AssistiveTechnologyImageController extends Controller
{
    protected AssistiveTechnologyImageService $imageService;

    public function __construct(AssistiveTechnologyImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function store(AssistiveTechnologyImageRequest $request, AssistiveTechnology $assistiveTechnology): RedirectResponse
    {
        $this->imageService->store(
            $assistiveTechnology,
            $request->file('image')
        );

        return redirect()
            ->route('inclusive-radar.assistive-technologies.edit', $assistiveTechnology)
            ->with('success', 'Imagem adicionada com sucesso!');
    }

    public function destroy(AssistiveTechnologyImage $image): RedirectResponse
    {
        $technology = $this->imageService->delete($image);

        return redirect()
            ->route('inclusive-radar.assistive-technologies.edit', $technology)
            ->with('success', 'Imagem removida com sucesso!');
    }
}
