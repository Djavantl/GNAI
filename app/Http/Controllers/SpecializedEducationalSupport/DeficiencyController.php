<?php

namespace App\Http\Controllers\SpecializedEducationalSupport;

use App\Http\Controllers\Controller;
use App\Http\Requests\SpecializedEducationalSupport\DeficiencyRequest;
use App\Models\SpecializedEducationalSupport\Deficiency;
use App\Services\SpecializedEducationalSupport\DeficiencyService;
use Illuminate\Http\Request;
use Exception;

class DeficiencyController extends Controller
{
    protected DeficiencyService $service;

    public function __construct(DeficiencyService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        try {
            $deficiencies = $this->service->index($request->all());

            if ($request->ajax()) {
                return view('pages.specialized-educational-support.deficiencies.partials.table', compact('deficiencies'))->render();
            }

            return view('pages.specialized-educational-support.deficiencies.index', compact('deficiencies'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Erro ao carregar as deficiências: ' . $e->getMessage());
        }
    }

    public function show(Deficiency $deficiency)
    {
        try {
            return view('pages.specialized-educational-support.deficiencies.show', compact('deficiency'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Erro ao exibir a deficiência: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            return view('pages.specialized-educational-support.deficiencies.create');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Erro ao carregar formulário de criação: ' . $e->getMessage());
        }
    }

    public function store(DeficiencyRequest $request)
    {
        try {
            $this->service->store($request->validated());

            return redirect()
                ->route('specialized-educational-support.deficiencies.index')
                ->with('success', 'Deficiência criada com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function edit(Deficiency $deficiency)
    {
        try {
            return view('pages.specialized-educational-support.deficiencies.edit', compact('deficiency'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Erro ao carregar formulário de edição: ' . $e->getMessage());
        }
    }

    public function update(DeficiencyRequest $request, Deficiency $deficiency)
    {
        try {
            $this->service->update($deficiency, $request->validated());

            return redirect()
                ->route('specialized-educational-support.deficiencies.index')
                ->with('success', 'Deficiência atualizada com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function toggleActive(Deficiency $deficiency)
    {
        try {
            $this->service->toggleActive($deficiency);

            $message = $deficiency->fresh()->is_active 
                ? 'Deficiência ativada com sucesso!' 
                : 'Deficiência desativada com sucesso!';

            return redirect()
                ->route('specialized-educational-support.deficiencies.index')
                ->with('success', $message);
        } catch (Exception $e) {
            return redirect()
                ->route('specialized-educational-support.deficiencies.index')
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(Deficiency $deficiency)
    {
        try {
            $this->service->delete($deficiency);

            return redirect()
                ->route('specialized-educational-support.deficiencies.index')
                ->with('success', 'Deficiência removida!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }
}