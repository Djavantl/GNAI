<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\BarrierRequest;
use App\Models\InclusiveRadar\Barrier;
use App\Services\InclusiveRadar\BarrierService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;
use Exception;

class BarrierController extends Controller
{
    public function __construct(protected BarrierService $service) {}

    public function index(): View
    {
        return view('inclusive-radar.barriers.index', [
            'barriers' => $this->service->listAll()
        ]);
    }

    public function create(): View
    {
        return view(
            'inclusive-radar.barriers.create',
            $this->service->getCreateData()
        );
    }

    public function store(BarrierRequest $request): RedirectResponse
    {
        try {
            $this->service->store($request->validated());

            return redirect()
                ->route('inclusive-radar.barriers.index')
                ->with('success', 'Barreira criada com sucesso!');
        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erro ao processar cadastro: ' . $e->getMessage());
        }
    }

    public function edit(Barrier $barrier): View
    {
        return view(
            'inclusive-radar.barriers.edit',
            $this->service->getEditData($barrier)
        );
    }

    public function update(BarrierRequest $request, Barrier $barrier): RedirectResponse
    {
        try {
            $this->service->update($barrier, $request->validated());

            return redirect()
                ->route('inclusive-radar.barriers.index')
                ->with('success', 'Barreira atualizada com sucesso!');
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ocorreu um erro inesperado: ' . $e->getMessage());
        }
    }

    public function toggleActive(Barrier $barrier): RedirectResponse
    {
        $this->service->toggleActive($barrier);

        return redirect()
            ->back()
            ->with('success', 'Status atualizado com sucesso!');
    }

    public function destroy(Barrier $barrier): RedirectResponse
    {
        try {
            $this->service->delete($barrier);

            return redirect()
                ->route('inclusive-radar.barriers.index')
                ->with('success', 'Barreira removida com sucesso!');
        } catch (ValidationException $e) {
            return redirect()
                ->route('inclusive-radar.barriers.index')
                ->withErrors($e->validator);
        } catch (Exception $e) {
            return redirect()
                ->route('inclusive-radar.barriers.index')
                ->with('error', 'Erro ao excluir: ' . $e->getMessage());
        }
    }
}
