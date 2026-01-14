<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\BarrierStatusRequest;
use App\Models\InclusiveRadar\BarrierStatus;
use App\Services\InclusiveRadar\BarrierStatusService;

class BarrierStatusController extends Controller
{

    protected BarrierStatusService $service;

    public function __construct(BarrierStatusService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $statuses = $this->service->listAll();

        return view('inclusive-radar.barrier-statuses.index', compact('statuses'));
    }

    public function create()
    {
        return view('inclusive-radar.barrier-statuses.create');
    }

    public function store(BarrierStatusRequest $request)
    {
        $this->service->store($request->validated());

        return redirect()
            ->route('inclusive-radar.barrier-statuses.index')
            ->with('success', 'Status criado com sucesso!');
    }

    public function show(BarrierStatus $barrierStatus)
    {
        //
    }

    public function edit(BarrierStatus $barrierStatus)
    {
        return view(
            'inclusive-radar.barrier-statuses.edit',
            compact('barrierStatus')
        );
    }

    public function update(BarrierStatusRequest $request, BarrierStatus $barrierStatus)
    {
        $this->service->update($barrierStatus, $request->validated());

        return redirect()
            ->route('inclusive-radar.barrier-statuses.index')
            ->with('success', 'Status atualizado com sucesso!');
    }

    public function toggleActive(BarrierStatus $barrierStatus)
    {
        $status = $this->service->toggleActive($barrierStatus);

        $message = $status->is_active
            ? 'Status ativado com sucesso!'
            : 'Status desativado com sucesso!';

        return redirect()
            ->route('inclusive-radar.barrier-statuses.index')
            ->with('success', $message);
    }

    public function destroy(BarrierStatus $barrierStatus)
    {
        $this->service->delete($barrierStatus);

        return redirect()
            ->route('inclusive-radar.barrier-statuses.index')
            ->with('success', 'Registro removido!');
    }

}
