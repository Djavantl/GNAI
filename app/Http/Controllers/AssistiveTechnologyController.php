<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssistiveTechnologyRequest;
use App\Models\AssistiveTechnology;
use App\Models\Deficiency;
use App\Services\AssistiveTechnologyService;

class AssistiveTechnologyController extends Controller
{
    protected AssistiveTechnologyService $service;

    public function __construct(AssistiveTechnologyService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $technologies = $this->service->listAll();
        return view('assistive-technologies.index', compact('technologies'));
    }

    public function create()
    {
        $deficiencies = Deficiency::where('is_active', true)->get();
        return view('assistive-technologies.create', compact('deficiencies'));
    }

    public function store(AssistiveTechnologyRequest $request)
    {
        $this->service->store($request->validated());

        return redirect()
            ->route('assistive-technologies.index')
            ->with('success', 'Tecnologia assistiva criada com sucesso!');
    }

    public function edit(AssistiveTechnology $assistiveTechnology)
    {
        $deficiencies = Deficiency::where('is_active', true)->get();
        return view('assistive-technologies.edit', compact('assistiveTechnology', 'deficiencies'));
    }

    public function update(AssistiveTechnologyRequest $request, AssistiveTechnology $assistiveTechnology)
    {
        $this->service->update($assistiveTechnology, $request->validated());

        return redirect()
            ->route('assistive-technologies.index')
            ->with('success', 'Tecnologia assistiva atualizada com sucesso!');
    }

    public function toggleActive(AssistiveTechnology $assistiveTechnology)
    {
        $tech = $this->service->toggleActive($assistiveTechnology);

        $message = $tech->is_active
            ? 'Tecnologia assistiva ativada com sucesso!'
            : 'Tecnologia assistiva desativada com sucesso!';

        return redirect()
            ->route('assistive-technologies.index')
            ->with('success', $message);
    }

    public function destroy(AssistiveTechnology $assistiveTechnology)
    {
        $this->service->delete($assistiveTechnology);

        return redirect()
            ->route('assistive-technologies.index')
            ->with('success', 'Tecnologia assistiva removida com sucesso!');
    }
}
