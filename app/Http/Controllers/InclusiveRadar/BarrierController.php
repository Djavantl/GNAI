<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\BarrierRequest;
use App\Models\InclusiveRadar\Barrier;
use App\Services\InclusiveRadar\BarrierService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BarrierController extends Controller
{
    public function __construct(
        protected BarrierService $service
    ) {}

    public function index(): View
    {
        return view('pages.inclusive-radar.barriers.index', [
            'barriers' => $this->service->listAll()
        ]);
    }

    public function create(): View
    {
        return view('pages.inclusive-radar.barriers.create', $this->service->getCreateData());
    }

    public function store(BarrierRequest $request): RedirectResponse
    {
        $this->service->store($request->validated());

        return redirect()
            ->route('inclusive-radar.barriers.index')
            ->with('success', 'Barreira identificada com sucesso!');
    }

    public function edit(Barrier $barrier): View
    {
        return view('pages.inclusive-radar.barriers.edit', $this->service->getEditData($barrier));
    }

    public function update(BarrierRequest $request, Barrier $barrier): RedirectResponse
    {
        $this->service->update($barrier, $request->validated());

        return redirect()
            ->route('inclusive-radar.barriers.index')
            ->with('success', 'Barreira atualizada com sucesso!');
    }

    public function toggleActive(Barrier $barrier): RedirectResponse
    {
        $this->service->toggleActive($barrier);

        return redirect()->back()->with('success', 'Status da barreira atualizado!');
    }

    public function destroy(Barrier $barrier): RedirectResponse
    {
        $this->service->delete($barrier);

        return redirect()
            ->route('inclusive-radar.barriers.index')
            ->with('success', 'Barreira removida com sucesso!');
    }
}
