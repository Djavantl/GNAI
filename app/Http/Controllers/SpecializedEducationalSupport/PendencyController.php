<?php

namespace App\Http\Controllers\SpecializedEducationalSupport;

use App\Http\Controllers\Controller;
use App\Models\SpecializedEducationalSupport\Pendency;
use App\Models\SpecializedEducationalSupport\Professional;
use App\Http\Requests\SpecializedEducationalSupport\PendencyRequest;
use App\Services\SpecializedEducationalSupport\PendencyService;
use App\Enums\Priority;
use Illuminate\Http\Request;
use Exception;

class PendencyController extends Controller
{
    protected PendencyService $service;

    public function __construct(PendencyService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        try {
            $pendencies = $this->service->index($request->all());

            $professionals = Professional::with('person')
                ->orderBy('id')
                ->get();

            if ($request->ajax()) {
                return view(
                    'pages.specialized-educational-support.pendencies.partials.table',
                    compact('pendencies')
                )->render();
            }

            return view(
                'pages.specialized-educational-support.pendencies.index',
                compact('pendencies', 'professionals')
            );
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Erro ao carregar pendências: ' . $e->getMessage());
        }
    }

    public function show(Pendency $pendency)
    {
        try {
            $pendency = $this->service->findById($pendency->id);

            return view(
                'pages.specialized-educational-support.pendencies.show',
                compact('pendency')
            );
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Erro ao exibir pendência: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $professionals = Professional::get();
            $priorities = collect(Priority::cases())
                ->mapWithKeys(fn($priority) => [
                    $priority->value => $priority->label()
                ])
                ->toArray();

            return view(
                'pages.specialized-educational-support.pendencies.create',
                compact('professionals', 'priorities')
            );
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Erro ao carregar formulário de criação: ' . $e->getMessage());
        }
    }

    public function store(PendencyRequest $request)
    {
        try {
            $this->service->create($request->validated());

            return redirect()
                ->route('specialized-educational-support.pendencies.index')
                ->with('success', 'Pendência criada com sucesso.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function edit(Pendency $pendency)
    {
        try {
            $professionals = Professional::get();
            $priorities = collect(Priority::cases())
                ->mapWithKeys(fn($priority) => [
                    $priority->value => $priority->label()
                ])
                ->toArray();

            return view(
                'pages.specialized-educational-support.pendencies.edit',
                compact('pendency', 'professionals', 'priorities')
            );
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Erro ao carregar formulário de edição: ' . $e->getMessage());
        }
    }

    public function update(PendencyRequest $request, Pendency $pendency)
    {
        try {
            $this->service->update($pendency, $request->validated());

            return redirect()
                ->route('specialized-educational-support.pendencies.index')
                ->with('success', 'Pendência atualizada com sucesso.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(Pendency $pendency)
    {
        try {
            $this->service->delete($pendency);

            return redirect()
                ->route('specialized-educational-support.pendencies.index')
                ->with('success', 'Pendência removida com sucesso.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Erro ao remover pendência: ' . $e->getMessage());
        }
    }

    public function myPendencies(Request $request)
    {
        try {
            $pendencies = $this->service->getMyPendencies($request->all());

            if ($request->ajax()) {
                return view(
                    'pages.specialized-educational-support.pendencies.partials.table',
                    compact('pendencies')
                )->render();
            }

            return view(
                'pages.specialized-educational-support.pendencies.my',
                compact('pendencies')
            );
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Erro ao carregar minhas pendências: ' . $e->getMessage());
        }
    }

    public function markAsCompleted(Pendency $pendency)
    {
        try {
            $this->service->markAsCompleted($pendency);

            return redirect()
                ->route('specialized-educational-support.pendencies.my')
                ->with('success', 'Pendência completada com sucesso.');
        } catch (Exception $e) {
            throw $e;
        }
    }
}