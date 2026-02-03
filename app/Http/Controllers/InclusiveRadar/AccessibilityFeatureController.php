<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\AccessibilityFeatureRequest;
use App\Models\InclusiveRadar\AccessibilityFeature;
use App\Services\InclusiveRadar\AccessibilityFeatureService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AccessibilityFeatureController extends Controller
{
    protected AccessibilityFeatureService $service;

    public function __construct(AccessibilityFeatureService $service)
    {
        $this->service = $service;
    }

    public function index(): View
    {
        $features = $this->service->listAll();
        return view('pages.inclusive-radar.accessibility-features.index', compact('features'));
    }

    public function create(): View
    {
        return view('pages.inclusive-radar.accessibility-features.create');
    }

    public function store(AccessibilityFeatureRequest $request): RedirectResponse
    {
        $this->service->store($request->validated());

        return redirect()->route('inclusive-radar.accessibility-features.index')
            ->with('success', 'Recurso de acessibilidade criado com sucesso!');
    }

    public function edit(AccessibilityFeature $accessibilityFeature): View
    {
        return view('pages.inclusive-radar.accessibility-features.edit', compact('accessibilityFeature'));
    }

    public function update(AccessibilityFeatureRequest $request, AccessibilityFeature $accessibilityFeature): RedirectResponse
    {
        $this->service->update($accessibilityFeature, $request->validated());

        return redirect()->route('inclusive-radar.accessibility-features.index')
            ->with('success', 'Recurso de acessibilidade atualizado com sucesso!');
    }

    public function toggleActive(AccessibilityFeature $accessibilityFeature): RedirectResponse
    {
        $feature = $this->service->toggleActive($accessibilityFeature);

        $message = $feature->is_active
            ? 'Recurso de acessibilidade ativado com sucesso!'
            : 'Recurso de acessibilidade desativado com sucesso!';

        return redirect()->route('inclusive-radar.accessibility-features.index')
            ->with('success', $message);
    }

    public function destroy(AccessibilityFeature $accessibilityFeature): RedirectResponse
    {
        $this->service->delete($accessibilityFeature);

        return redirect()->route('inclusive-radar.accessibility-features.index')
            ->with('success', 'Recurso de acessibilidade removido com sucesso!');
    }
}
