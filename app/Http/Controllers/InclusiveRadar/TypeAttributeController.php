<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\TypeAttributeRequest;
use App\Models\InclusiveRadar\TypeAttribute;
use App\Services\InclusiveRadar\TypeAttributeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TypeAttributeController extends Controller
{
    protected TypeAttributeService $service;

    public function __construct(TypeAttributeService $service)
    {
        $this->service = $service;
    }

    public function index(): View
    {
        $attributes = $this->service->listAll();
        return view('pages.inclusive-radar.type-attributes.index', compact('attributes'));
    }

    public function create(): View
    {
        return view('pages.inclusive-radar.type-attributes.create');
    }

    public function store(TypeAttributeRequest $request): RedirectResponse
    {
        $this->service->store($request->validated());

        return redirect()
            ->route('inclusive-radar.type-attributes.index')
            ->with('success', 'Atributo criado com sucesso.');
    }

    public function edit(TypeAttribute $typeAttribute): View
    {
        return view('pages.inclusive-radar.type-attributes.edit', compact('typeAttribute'));
    }

    public function update(TypeAttributeRequest $request, TypeAttribute $typeAttribute): RedirectResponse
    {
        $this->service->update($typeAttribute, $request->validated());

        return redirect()
            ->route('inclusive-radar.type-attributes.index')
            ->with('success', 'Atributo atualizado com sucesso.');
    }

    public function toggleActive(TypeAttribute $typeAttribute): RedirectResponse
    {
        $attribute = $this->service->toggleActive($typeAttribute);

        $message = $attribute->is_active
            ? 'Atributo ativado com sucesso!'
            : 'Atributo desativado com sucesso!';

        return redirect()
            ->back()
            ->with('success', $message);
    }

    public function destroy(TypeAttribute $typeAttribute): RedirectResponse
    {
        $this->service->delete($typeAttribute);

        return redirect()
            ->route('inclusive-radar.type-attributes.index')
            ->with('success', 'Atributo excluído com sucesso.');
    }
}
