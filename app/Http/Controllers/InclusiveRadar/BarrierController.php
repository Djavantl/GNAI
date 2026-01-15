<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\BarrierRequest;
use App\Models\InclusiveRadar\Barrier;
use App\Models\InclusiveRadar\Institution;
use App\Models\InclusiveRadar\Location;
use App\Models\InclusiveRadar\BarrierCategory;
use App\Models\InclusiveRadar\BarrierStatus;
use App\Models\SpecializedEducationalSupport\Deficiency;
use App\Services\InclusiveRadar\BarrierService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BarrierController extends Controller
{
    public function __construct(protected BarrierService $service) {}

    public function index(): View
    {
        $barriers = $this->service->listAll();
        return view('inclusive-radar.barriers.index', compact('barriers'));
    }

    public function create(): View
    {
        $institutions = Institution::with(['locations' => function($q) {
            $q->where('is_active', true);
        }])->where('is_active', true)->orderBy('name')->get();

        $categories = BarrierCategory::where('is_active', true)->get();
        $statuses = BarrierStatus::where('is_active', true)->get();
        $deficiencies = Deficiency::where('is_active', true)->get();

        return view('inclusive-radar.barriers.create', compact(
            'institutions',
            'categories',
            'statuses',
            'deficiencies'
        ));
    }

    public function store(BarrierRequest $request): RedirectResponse
    {
        $this->service->store($request->validated());

        return redirect()
            ->route('inclusive-radar.barriers.index')
            ->with('success', 'Sua contribuição foi registrada com sucesso! Obrigado por ajudar na acessibilidade.');
    }

    public function edit(Barrier $barrier)
    {
        $institutions = Institution::with(['locations' => function($q) {
            $q->where('is_active', true);
        }])->where('is_active', true)->orderBy('name')->get();

        $categories = BarrierCategory::where('is_active', true)->get();
        $statuses = BarrierStatus::where('is_active', true)->get();
        $deficiencies = Deficiency::where('is_active', true)->get();

        return view('inclusive-radar.barriers.edit', compact(
            'barrier',
            'institutions',
            'categories',
            'statuses',
            'deficiencies'
        ));
    }

    public function update(BarrierRequest $request, Barrier $barrier): RedirectResponse
    {
        $this->service->update($barrier, $request->validated());

        return redirect()
            ->route('inclusive-radar.barriers.index')
            ->with('success', 'Relato de barreira atualizado.');
    }

    public function toggleActive(Barrier $barrier): RedirectResponse
    {
        $this->service->toggleActive($barrier);
        return redirect()->back()->with('success', 'Status alterado.');
    }

    public function destroy(Barrier $barrier): RedirectResponse
    {
        $this->service->delete($barrier);
        return redirect()
            ->route('inclusive-radar.barriers.index')
            ->with('success', 'Relato removido.');
    }
}
