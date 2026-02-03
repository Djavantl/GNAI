<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tecnologia Assistiva - GNAI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 p-4 md:p-8">
<div class="max-w-6xl mx-auto bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">

    <div class="bg-slate-800 p-6 flex justify-between items-center text-white">
        <div class="flex items-center gap-4">
            <div class="bg-blue-600 p-3 rounded-lg">
                <i class="fas fa-edit text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold">Editar Recurso</h1>
                <p class="text-slate-400 text-sm">Atualizando: {{ $assistiveTechnology->name }}</p>
            </div>
        </div>
        <div class="text-right">
            <span class="block text-xs text-slate-400 uppercase font-bold tracking-widest">Patrimônio</span>
            <span class="text-lg font-mono">{{ $assistiveTechnology->asset_code ?? 'SEM CÓDIGO' }}</span>
        </div>
    </div>

    @if($errors->any())
        <div class="m-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700">
            <ul class="list-disc ml-5 text-sm">
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('inclusive-radar.assistive-technologies.update', $assistiveTechnology->id) }}" method="POST" enctype="multipart/form-data" class="p-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            <div class="lg:col-span-8 space-y-8">

                <section>
                    <h3 class="text-blue-700 font-bold uppercase text-xs tracking-wider mb-4 border-b pb-1">1. Informações do Equipamento</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700">Nome da Tecnologia *</label>
                            <input type="text" name="name" value="{{ old('name', $assistiveTechnology->name) }}" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 border p-2.5">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700">Descrição Detalhada</label>
                            <textarea name="description" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2.5">{{ old('description', $assistiveTechnology->description) }}</textarea>
                        </div>
                    </div>
                </section>

                <section>
                    <h3 class="text-slate-700 font-bold uppercase text-xs tracking-wider mb-4 flex items-center gap-2">
                        <i class="fas fa-history text-blue-500"></i> 2. Histórico de Vistorias
                    </h3>
                    <div class="space-y-3 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                        @forelse($assistiveTechnology->inspections()->with('images')->latest('inspection_date')->get() as $inspection)
                            <div class="border rounded-lg bg-white shadow-sm overflow-hidden">
                                <div class="bg-gray-50 px-4 py-2 border-b flex justify-between items-center">
                                    <span class="text-xs font-bold text-slate-600 italic">
                                        {{ $inspection->inspection_date->format('d/m/Y') }}
                                    </span>
                                    <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 border border-blue-200">
                                        {{ $inspection->type->label() }}
                                    </span>
                                </div>
                                <div class="p-4">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <span class="text-[10px] uppercase font-bold text-gray-400 block leading-none mb-1">Estado na época</span>
                                            <span class="text-sm font-bold {{ $inspection->state->value === 'excellent' ? 'text-green-600' : 'text-slate-700' }}">
                                                {{ $inspection->state->label() }}
                                            </span>
                                        </div>
                                        @if($inspection->description)
                                            <p class="text-xs text-gray-500 italic max-w-md bg-gray-50 p-2 rounded">"{{ $inspection->description }}"</p>
                                        @endif
                                    </div>
                                    @if($inspection->images->count() > 0)
                                        <div class="flex gap-2 overflow-x-auto pb-2">
                                            @foreach($inspection->images as $img)
                                                <a href="{{ asset('storage/' . $img->path) }}" target="_blank" class="flex-shrink-0">
                                                    <img src="{{ asset('storage/' . $img->path) }}" class="h-14 w-14 object-cover rounded border border-gray-200 hover:border-blue-500 transition-colors">
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center border-2 border-dashed rounded-xl text-gray-400">Sem vistorias anteriores.</div>
                        @endforelse
                    </div>
                </section>

                <section class="bg-blue-50 p-6 rounded-xl border border-blue-200 shadow-inner">
                    <h3 class="text-blue-800 font-bold uppercase text-xs tracking-wider mb-4 flex items-center gap-2">
                        <i class="fas fa-plus-circle"></i> Nova Atualização de Estado / Vistoria
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase">Estado Atual *</label>
                            <select name="conservation_state" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2 bg-white text-sm">
                                @foreach(\App\Enums\InclusiveRadar\ConservationState::cases() as $state)
                                    <option value="{{ $state->value }}" {{ old('conservation_state', $assistiveTechnology->conservation_state->value) == $state->value ? 'selected' : '' }}>
                                        {{ $state->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase">Tipo do Registro</label>
                            <select name="inspection_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2 bg-white text-sm">
                                @foreach(\App\Enums\InclusiveRadar\InspectionType::cases() as $type)
                                    @if($type !== \App\Enums\InclusiveRadar\InspectionType::INITIAL)
                                        <option value="{{ $type->value }}" {{ old('inspection_type') == $type->value ? 'selected' : '' }}>
                                            {{ $type->label() }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase">Data</label>
                            <input type="date" name="inspection_date" value="{{ date('Y-m-d') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2 text-sm">
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-xs font-bold text-slate-600 uppercase">Notas da nova atualização (Vistoria)</label>
                            <textarea name="inspection_description" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2 text-sm" placeholder="O que mudou no equipamento?"></textarea>
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-xs font-bold text-slate-600 uppercase">Novas Fotos</label>
                            <input type="file" name="images[]" multiple class="mt-1 block w-full text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-4 file:rounded file:border-0 file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                        </div>
                    </div>
                </section>
            </div>

            <div class="lg:col-span-4 space-y-6">

                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                    <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-boxes text-blue-500"></i> Inventário
                    </h3>

                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase">Categoria</label>
                        <select name="type_id" id="type_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2 bg-gray-50 text-sm">
                            <option value="">Selecione um tipo...</option>
                            @foreach($resourceTypes as $type)
                                <option value="{{ $type->id }}"
                                        data-digital="{{ $type->is_digital ? '1' : '0' }}"
                                    {{ old('type_id', $assistiveTechnology->type_id) == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @php $activeLoans = $assistiveTechnology->loans()->whereIn('status', ['active', 'late'])->count(); @endphp
                    <div id="quantity_container" class="mb-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase">Quantidade Total</label>
                        <input type="number" name="quantity" id="quantity_field" value="{{ old('quantity', $assistiveTechnology->quantity) }}" min="{{ $activeLoans }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2 font-bold text-blue-700">
                        @if($activeLoans > 0)
                            <div class="mt-2 flex items-center gap-1 text-[10px] font-bold text-amber-600 uppercase bg-amber-50 p-1.5 rounded border border-amber-100">
                                <i class="fas fa-lock"></i> {{ $activeLoans }} unidades em uso (bloqueadas)
                            </div>
                        @endif
                    </div>

                    <div id="asset_code_container">
                        <label class="block text-xs font-bold text-gray-500 uppercase">Código Patrimonial</label>
                        <input type="text" name="asset_code" value="{{ old('asset_code', $assistiveTechnology->asset_code) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2 text-sm font-mono">
                    </div>
                </div>

                <div id="dynamic-attributes-container" class="hidden bg-white p-5 rounded-xl border border-blue-100 shadow-sm">
                    <h3 class="text-xs font-bold text-blue-800 uppercase mb-4 tracking-tighter">Especificações Técnicas</h3>
                    <div id="dynamic-attributes" class="space-y-4"></div>
                </div>

                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                    <h3 class="text-sm font-bold text-gray-800 mb-4">Público-alvo</h3>
                    <div class="space-y-2 max-h-52 overflow-y-auto pr-2 custom-scrollbar">
                        @foreach($deficiencies as $def)
                            <label class="flex items-center p-2 rounded hover:bg-slate-50 cursor-pointer border border-transparent transition-all">
                                <input type="checkbox" name="deficiencies[]" value="{{ $def->id }}"
                                       {{ in_array($def->id, old('deficiencies', $assistiveTechnology->deficiencies->pluck('id')->toArray())) ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 rounded border-gray-300">
                                <span class="ml-3 text-sm text-gray-600">{{ $def->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="p-4 bg-white border rounded-lg shadow-sm">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2">Status Operacional</label>
                        <select name="status_id" class="w-full border-gray-300 rounded-md shadow-sm border p-2 text-sm font-bold text-slate-700">
                            @foreach(\App\Models\InclusiveRadar\ResourceStatus::where('is_active', true)->get() as $status)
                                <option value="{{ $status->id }}" {{ old('status_id', $assistiveTechnology->status_id) == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <label class="flex items-center gap-3 p-4 bg-white border rounded-lg shadow-sm cursor-pointer hover:bg-slate-50">
                        <input type="checkbox" name="requires_training" value="1" {{ old('requires_training', $assistiveTechnology->requires_training) ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded">
                        <span class="text-sm font-bold text-slate-700">Requer Treinamento</span>
                    </label>

                    <label class="flex items-center gap-3 p-4 bg-white border rounded-lg shadow-sm cursor-pointer hover:bg-slate-50">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $assistiveTechnology->is_active) ? 'checked' : '' }} class="w-5 h-5 text-green-600 rounded">
                        <span class="text-sm font-bold text-slate-700">Cadastro Ativo</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="mt-10 pt-6 border-t flex flex-col-reverse md:flex-row justify-end gap-3">
            <a href="{{ route('inclusive-radar.assistive-technologies.index') }}"
               class="px-8 py-3 rounded-lg text-gray-500 hover:text-gray-800 font-bold transition-all text-center">
                Cancelar e Sair
            </a>
            <button type="submit" class="px-12 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold shadow-lg shadow-blue-200 transition-all transform hover:-translate-y-0.5">
                <i class="fas fa-check-circle mr-2"></i> Salvar Alterações
            </button>
        </div>
    </form>
</div>

<script>
    const typeSelect = document.getElementById('type_id');
    const dynamicAttrContainer = document.getElementById('dynamic-attributes-container');
    const dynamicAttrList = document.getElementById('dynamic-attributes');
    const assetContainer = document.getElementById('asset_code_container');
    const quantityContainer = document.getElementById('quantity_container');
    const quantityField = document.getElementById('quantity_field');

    function toggleFields() {
        const isDigital = typeSelect.options[typeSelect.selectedIndex]?.dataset.digital === '1';
        assetContainer.style.display = isDigital ? 'none' : 'block';
        quantityContainer.style.display = isDigital ? 'none' : 'block';
        if (isDigital) quantityField.value = '';
    }

    function loadAttributes(typeId, currentValues = {}) {
        if (!typeId) {
            dynamicAttrContainer.classList.add('hidden');
            return;
        }

        fetch(`/inclusive-radar/resource-types/${typeId}/attributes`)
            .then(res => res.json())
            .then(data => {
                dynamicAttrList.innerHTML = '';
                if (data.length > 0) {
                    dynamicAttrContainer.classList.remove('hidden');
                    data.forEach(attr => {
                        const val = currentValues[attr.id] || '';
                        const div = document.createElement('div');
                        div.className = "p-3 bg-slate-50 rounded border border-slate-100";

                        if (attr.field_type === 'boolean') {
                            div.innerHTML = `
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="hidden" name="attributes[${attr.id}]" value="0">
                                    <input type="checkbox" name="attributes[${attr.id}]" value="1" ${val == '1' ? 'checked' : ''} class="rounded text-blue-600">
                                    <span class="text-xs font-bold text-slate-700">${attr.label}</span>
                                </label>`;
                        } else {
                            div.innerHTML = `
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">${attr.label}</label>
                                <input type="${['integer', 'decimal'].includes(attr.field_type) ? 'number' : 'text'}"
                                       name="attributes[${attr.id}]" value="${val}"
                                       class="w-full border-gray-300 rounded border p-1.5 text-xs focus:ring-blue-500">`;
                        }
                        dynamicAttrList.appendChild(div);
                    });
                } else {
                    dynamicAttrContainer.classList.add('hidden');
                }
            });
    }

    // Passamos os valores atuais vindo do service
    const currentAttrValues = @json($attributeValues ?? []);

    typeSelect.addEventListener('change', (e) => {
        toggleFields();
        loadAttributes(e.target.value, {});
    });

    // Início
    if(typeSelect.value) {
        toggleFields();
        loadAttributes(typeSelect.value, currentAttrValues);
    }
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 5px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
</body>
</html>
