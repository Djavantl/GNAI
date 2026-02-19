<?php

namespace App\Http\Controllers\SpecializedEducationalSupport;

use App\Http\Controllers\Controller;
use App\Http\Requests\SpecializedEducationalSupport\ProfessionalRequest;
use App\Models\SpecializedEducationalSupport\Person;
use App\Models\SpecializedEducationalSupport\Position;
use App\Models\SpecializedEducationalSupport\Professional;
use App\Services\SpecializedEducationalSupport\ProfessionalService;
use App\Models\SpecializedEducationalSupport\Semester;
use Illuminate\Http\Request;

class ProfessionalController extends Controller
{
    protected ProfessionalService $service;

    public function __construct(ProfessionalService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $professionals = $this->service->index($request->all());

        $semesters = Semester::orderByDesc('year')
            ->orderByDesc('term')
            ->get(['id', 'label']);

        if ($request->ajax()) {
            return view(
                'pages.specialized-educational-support.professionals.partials.table',
                compact('professionals', 'semesters')
            )->render();
        }

        return view(
            'pages.specialized-educational-support.professionals.index',
            compact('professionals', 'semesters')
        );
    }

    public function show(Professional $professional)
    {
        $professional = $this->service->show($professional);
        return view('pages.specialized-educational-support.professionals.show', compact('professional'));
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
