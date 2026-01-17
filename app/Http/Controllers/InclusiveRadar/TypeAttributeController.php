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
    public function __construct(private TypeAttributeService $service) {}

    public function index(): View
    {
        $attributes = TypeAttribute::orderBy('label')->get();
        return view('inclusive-radar.type-attributes.index', compact('attributes'));
    }

    public function create(): View
    {
        return view('inclusive-radar.type-attributes.create');
    }

    public function store(TypeAttributeRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()
            ->route('inclusive-radar.type-attributes.index')
            ->with('success', 'Atributo criado com sucesso.');
    }

    public function edit(TypeAttribute $type_attribute): View
    {
        return view('inclusive-radar.type-attributes.edit', ['attribute' => $type_attribute]);
    }

    public function update(TypeAttributeRequest $request, TypeAttribute $type_attribute): RedirectResponse
    {
        $this->service->update($type_attribute, $request->validated());

        return redirect()
            ->route('inclusive-radar.type-attributes.index')
            ->with('success', 'Atributo atualizado com sucesso.');
    }

    public function toggle(TypeAttribute $type_attribute): RedirectResponse
    {
        $this->service->toggleActive($type_attribute);

        return redirect()
            ->back()
            ->with('success', 'Status de ativo alterado com sucesso.');
    }

    public function destroy(TypeAttribute $type_attribute): RedirectResponse
    {
        $this->service->delete($type_attribute);

        return redirect()
            ->route('inclusive-radar.type-attributes.index')
            ->with('success', 'Atributo excluído com sucesso.');
    }
}
