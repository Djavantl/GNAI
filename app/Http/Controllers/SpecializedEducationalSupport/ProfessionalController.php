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
use Exception;

class ProfessionalController extends Controller
{
    protected ProfessionalService $service;

    public function __construct(ProfessionalService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        try {
            $professionals = $this->service->index($request->all());

            $semesters = $this->semesters();
            $positions = Position::orderBy('name')
                ->get(['id', 'name']);

            if ($request->ajax()) {
                return view(
                    'pages.specialized-educational-support.professionals.partials.table',
                    compact('professionals', 'semesters', 'positions')
                )->render();
            }

            return view(
                'pages.specialized-educational-support.professionals.index',
                compact('professionals', 'semesters', 'positions')
            );
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Erro ao listar profissionais: ' . $e->getMessage());
        }
    }

    public function show(Professional $professional)
    {
        try {
            $professional = $this->service->show($professional);
            return view('pages.specialized-educational-support.professionals.show', compact('professional'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Erro ao exibir profissional: ' . $e->getMessage());
        }
    }


    public function create()
    {
        try {
            $positions = Position::orderBy('name')->get();

            return view(
                'pages.specialized-educational-support.professionals.create',
                compact('positions')
            );
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Erro ao carregar formulário: ' . $e->getMessage());
        }
    }

    public function store(ProfessionalRequest $request)
    {
        try {
            $this->service->create($request->validated());

            return redirect()
                ->route('specialized-educational-support.professionals.index')
                ->with('success', 'Profissional cadastrado com sucesso.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function edit(Professional $professional)
    {
        try {
            $positions = Position::orderBy('name')->get();

            return view(
                'pages.specialized-educational-support.professionals.edit',
                compact('professional', 'positions')
            );
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Erro ao carregar edição: ' . $e->getMessage());
        }
    }

    public function update(
        ProfessionalRequest $request,
        Professional $professional
    ) {
        try {
            $this->service->update($professional, $request->validated());

            return redirect()
                ->route('specialized-educational-support.professionals.index')
                ->with('success', 'Profissional atualizado com sucesso.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(Professional $professional)
    {
        try {
            $this->service->delete($professional);

            return redirect()
                ->route('specialized-educational-support.professionals.index')
                ->with('success', 'Profissional removido com sucesso.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Erro ao remover profissional: ' . $e->getMessage());
        }
    }
}