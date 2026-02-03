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
    public function __construct(
        protected AssistiveTechnologyService $service
    ) {}

    public function index(): View
    {
        return view('pages.inclusive-radar.assistive-technologies.index', [
            'assistiveTechnologies' => $this->service->listAll()
        ]);
    }

    public function create(): View
    {
        return view('pages.inclusive-radar.assistive-technologies.create', $this->service->getCreateData());
    }

    public function store(AssistiveTechnologyRequest $request): RedirectResponse
    {
        $this->service->store($request->validated());

        return redirect()
            ->route('inclusive-radar.assistive-technologies.index')
            ->with('success', 'Tecnologia assistiva criada com sucesso!');
    }

    public function edit(AssistiveTechnology $assistiveTechnology): View
    {
        return view('pages.inclusive-radar.assistive-technologies.edit', $this->service->getEditData($assistiveTechnology));
    }

    public function update(AssistiveTechnologyRequest $request, AssistiveTechnology $assistiveTechnology): RedirectResponse
    {
        $this->service->update($assistiveTechnology, $request->validated());

        return redirect()
            ->route('inclusive-radar.assistive-technologies.index')
            ->with('success', 'Tecnologia assistiva atualizada com sucesso!');
    }

    public function toggleActive(AssistiveTechnology $assistiveTechnology): RedirectResponse
    {
        $this->service->toggleActive($assistiveTechnology);

        return redirect()->back()->with('success', 'Status atualizado com sucesso!');
    }

    public function destroy(AssistiveTechnology $assistiveTechnology): RedirectResponse
    {
        $this->service->delete($assistiveTechnology);

        return redirect()
            ->route('inclusive-radar.assistive-technologies.index')
            ->with('success', 'Tecnologia removida com sucesso!');
    }
}
