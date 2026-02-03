<?php

namespace App\Http\Controllers\SpecializedEducationalSupport;

use App\Http\Controllers\Controller;
use App\Http\Requests\SpecializedEducationalSupport\ProfessionalRequest;
use App\Models\SpecializedEducationalSupport\Person;
use App\Models\SpecializedEducationalSupport\Position;
use App\Models\SpecializedEducationalSupport\Professional;
use App\Services\SpecializedEducationalSupport\ProfessionalService;

class ProfessionalController extends Controller
{
    protected ProfessionalService $service;

    public function __construct(ProfessionalService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $professionals = $this->service->all();

        return view(
            'pages.specialized-educational-support.professionals.index',
            compact('professionals')
        );
    }

    public function create()
    {
        $positions = Position::orderBy('name')->get();

        return view(
            'pages.specialized-educational-support.professionals.create',
            compact('positions')
        );
    }

    public function store(ProfessionalRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()
            ->route('specialized-educational-support.professionals.index')
            ->with('success', 'Profissional cadastrado com sucesso.');
    }

    public function edit(Professional $professional)
    {
        $positions = Position::orderBy('name')->get();

        return view(
            'pages.specialized-educational-support.professionals.edit',
            compact('professional', 'positions')
        );
    }

    public function update(
        ProfessionalRequest $request,
        Professional $professional
    ) {
        $this->service->update($professional, $request->validated());

        return redirect()
            ->route('specialized-educational-support.professionals.index')
            ->with('success', 'Profissional atualizado com sucesso.');
    }

    public function destroy(Professional $professional)
    {
        $this->service->delete($professional);

        return redirect()
            ->route('specialized-educational-support.professionals.index')
            ->with('success', 'Profissional removido com sucesso.');
    }
}
