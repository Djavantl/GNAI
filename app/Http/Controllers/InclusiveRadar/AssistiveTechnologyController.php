<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\AssistiveTechnologyRequest;
use App\Models\InclusiveRadar\AssistiveTechnology;
use App\Services\InclusiveRadar\AssistiveTechnologyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AssistiveTechnologyController extends Controller
{
    protected AssistiveTechnologyService $assistiveTechnologyService;

    public function __construct(AssistiveTechnologyService $assistiveTechnologyService)
    {
        $this->assistiveTechnologyService = $assistiveTechnologyService;
    }

    public function index(): View
    {
        $assistiveTechnologies =
            $this->assistiveTechnologyService->listAll();

        return view(
            'inclusive-radar.assistive-technologies.index',
            compact('assistiveTechnologies')
        );
    }

    public function create(): View
    {
        return view(
            'inclusive-radar.assistive-technologies.create',
            $this->assistiveTechnologyService->getCreateData()
        );
    }

    public function store(AssistiveTechnologyRequest $request): RedirectResponse
    {
        $this->assistiveTechnologyService->store(
            $request->validated()
        );

        return redirect()
            ->route('inclusive-radar.assistive-technologies.index')
            ->with('success', 'Tecnologia assistiva criada com sucesso!');
    }

    public function edit(AssistiveTechnology $assistiveTechnology): View
    {
        return view(
            'inclusive-radar.assistive-technologies.edit',
            $this->assistiveTechnologyService->getEditData(
                $assistiveTechnology
            )
        );
    }

    public function update(AssistiveTechnologyRequest $request, AssistiveTechnology $assistiveTechnology): RedirectResponse
    {
        $this->assistiveTechnologyService->update(
            $assistiveTechnology,
            $request->validated()
        );

        return redirect()
            ->route('inclusive-radar.assistive-technologies.index')
            ->with('success', 'Tecnologia assistiva atualizada com sucesso!');
    }

    public function toggleActive(AssistiveTechnology $assistiveTechnology): RedirectResponse
    {
        $this->assistiveTechnologyService->toggleActive(
            $assistiveTechnology
        );

        return redirect()
            ->route('inclusive-radar.assistive-technologies.index')
            ->with('success', 'Status atualizado com sucesso!');
    }

    public function destroy(AssistiveTechnology $assistiveTechnology): RedirectResponse
    {
        $this->assistiveTechnologyService->delete(
            $assistiveTechnology
        );

        return redirect()
            ->route('inclusive-radar.assistive-technologies.index')
            ->with('success', 'Tecnologia assistiva removida com sucesso!');
    }
}
