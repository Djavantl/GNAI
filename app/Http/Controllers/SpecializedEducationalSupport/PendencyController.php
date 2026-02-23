<?php

namespace App\Http\Controllers\SpecializedEducationalSupport;

use App\Http\Controllers\Controller;
use App\Models\SpecializedEducationalSupport\Pendency;
use App\Models\SpecializedEducationalSupport\Professional;
use App\Http\Requests\SpecializedEducationalSupport\PendencyRequest;
use App\Services\SpecializedEducationalSupport\PendencyService;
use App\Enums\Priority;
use Illuminate\Http\Request;


class PendencyController extends Controller
{
    protected PendencyService $service;

    public function __construct(PendencyService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
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
    }

    public function store(PendencyRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()
            ->route('specialized-educational-support.pendencies.index')
            ->with('success', 'Pendência criada com sucesso.');
    }

    public function edit(Pendency $pendency)
    {
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
    }

    public function update(PendencyRequest $request, Pendency $pendency)
    {
        $this->service->update($pendency, $request->validated());

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

    public function myPendencies(Request $request)
    {
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
    }

    public function markAsCompleted(Pendency $pendency)
    {
        $this->service->markAsCompleted($pendency);

        return redirect()
            ->route('specialized-educational-support.pendencies.my')
            ->with('success', 'Pendência completada com sucesso.');
    }
}
