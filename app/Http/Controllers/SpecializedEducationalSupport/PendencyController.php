<?php

namespace App\Http\Controllers\SpecializedEducationalSupport;

use App\Http\Controllers\Controller;
use App\Models\SpecializedEducationalSupport\Pendency;
use App\Models\SpecializedEducationalSupport\Professional;
use App\Http\Requests\SpecializedEducationalSupport\PendencyRequest;
use App\Services\SpecializedEducationalSupport\PendencyService;

class PendencyController extends Controller
{
    protected PendencyService $service;

    public function __construct(PendencyService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $pendencies = $this->service->getAll();

        return view(
            'pages.specialized-educational-support.pendencies.index',
            compact('pendencies')
        );
    }

    public function show(Pendency $pendency)
    {
        $pendency = $this->service->findById($pendency->id);

        return view(
            'pages.specialized-educational-support.pendencies.show',
            compact('pendency')
        );
    }

    public function create()
    {
        $professionals = Professional::orderBy('name')->get();

        return view(
            'pages.specialized-educational-support.pendencies.create',
            compact('professionals')
        );
    }

    public function store(PendencyRequest $request)
    {
        $this->service->create($request->validatedData());

        return redirect()
            ->route('specialized-educational-support.pendencies.index')
            ->with('success', 'Pendência criada com sucesso.');
    }

    public function edit(Pendency $pendency)
    {
        $professionals = Professional::orderBy('name')->get();

        return view(
            'pages.specialized-educational-support.pendencies.edit',
            compact('pendency', 'professionals')
        );
    }

    public function update(PendencyRequest $request, Pendency $pendency)
    {
        $this->service->update($pendency, $request->validatedData());

        return redirect()
            ->route('specialized-educational-support.pendencies.index')
            ->with('success', 'Pendência atualizada com sucesso.');
    }

    public function destroy(Pendency $pendency)
    {
        $this->service->delete($pendency);

        return redirect()
            ->route('specialized-educational-support.pendencies.index')
            ->with('success', 'Pendência removida com sucesso.');
    }

    public function myPendencies()
    {
        $pendencies = $this->service->getMyPendencies();

        return view(
            'pages.specialized-educational-support.pendencies.my',
            compact('pendencies')
        );
    }

    public function markAsCompleted(Pendency $pendency)
    {
        $this->service->markAsCompleted($pendency);

        return redirect()
            ->route('specialized-educational-support.pendencies.my')
            ->with('success', 'Pendência completada com sucesso.');
    }
}
