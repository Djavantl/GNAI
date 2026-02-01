<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Material Pedagógico Acessível - GNAI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-4 md:p-8">
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow border-t-4 border-blue-600">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-bold text-gray-800">Editar Material Pedagógico</h1>
            @if(!$material->is_active)
                <span class="bg-amber-100 text-amber-700 text-[10px] font-bold px-2 py-1 rounded border border-amber-200 uppercase tracking-wider">Oculto no Catálogo</span>
            @endif
        </div>
        <span class="text-sm text-gray-500 font-mono bg-gray-100 px-2 py-1 rounded">ID: #{{ $material->id }}</span>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded shadow-sm text-sm">
            <p class="font-bold mb-1 italic">Atenção: Existem erros no preenchimento.</p>
            <ul class="list-disc ml-5">
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('inclusive-radar.accessible-educational-materials.update', $material->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-6">
            {{-- IDENTIFICAÇÃO BÁSICA --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="block font-bold text-gray-700 mb-1">Título do Material *</label>
                    <input type="text" name="name" value="{{ old('name', $material->name) }}"
                           class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1 text-sm uppercase">Tipo / Categoria *</label>
                    <select name="type_id" id="type_id" class="w-full border p-2 rounded bg-white focus:ring-2 focus:ring-blue-500">
                        @foreach(\App\Models\InclusiveRadar\ResourceType::where('for_educational_material', true)->where('is_active', true)->get() as $type)
                            <option value="{{ $type->id }}" data-digital="{{ $type->is_digital ? '1' : '0' }}"
                                {{ old('type_id', $material->type_id) == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <hr>

            {{-- HISTÓRICO DE VISTORIAS (ESTILO TA) --}}
            <div class="mt-2">
                <label class="block font-bold mb-4 text-gray-800 uppercase text-sm tracking-wide">
                    <i class="fas fa-history mr-2 text-blue-600"></i>Histórico de Vistorias e Fotos
                </label>

                <div class="space-y-4 max-h-[400px] overflow-y-auto pr-2">
                    @forelse($material->inspections()->with('images')->latest('inspection_date')->get() as $inspection)
                        <div class="border rounded-lg bg-gray-50 overflow-hidden shadow-sm text-sm">
                            <div class="bg-gray-100 px-4 py-2 border-b flex justify-between items-center">
                                <span class="text-xs font-bold text-blue-700">
                                    <i class="far fa-calendar-alt mr-1"></i> {{ $inspection->inspection_date->format('d/m/Y') }}
                                </span>
                                <span class="text-[10px] font-bold uppercase px-2 py-1 rounded bg-white border border-gray-300">
                                    {{ $inspection->type->label() }}
                                </span>
                            </div>
                            <div class="p-4">
                                <div class="mb-3 flex justify-between items-start">
                                    <div>
                                        <p class="text-[10px] font-bold text-gray-500 uppercase">Estado:</p>
                                        <p class="font-semibold text-gray-800">{{ $inspection->state->label() }}</p>
                                    </div>
                                    @if($inspection->description)
                                        <div class="max-w-[60%] text-right text-xs text-gray-600 italic">
                                            "{{ $inspection->description }}"
                                        </div>
                                    @endif
                                </div>
                                @if($inspection->images->count() > 0)
                                    <div class="grid grid-cols-4 sm:grid-cols-6 gap-2">
                                        @foreach($inspection->images as $img)
                                            <a href="{{ asset('storage/' . $img->path) }}" target="_blank" class="border rounded p-1 bg-white hover:border-blue-500 transition block">
                                                <img src="{{ asset('storage/' . $img->path) }}" class="h-14 w-full object-cover rounded">
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 border-2 border-dashed rounded-lg text-gray-400">Nenhuma vistoria registrada.</div>
                    @endforelse
                </div>
            </div>

            {{-- NOVA VISTORIA / ATUALIZAÇÃO (Sincronizada com Service) --}}
            <div class="bg-blue-50 p-5 rounded-lg border-2 border-blue-200">
                <h3 class="text-blue-800 font-bold mb-4 flex items-center gap-2 uppercase text-xs">
                    <i class="fas fa-plus-circle"></i> Atualizar Estado / Adicionar Fotos
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1 text-[10px] uppercase">Novo Estado *</label>
                        <select name="conservation_state" class="w-full border p-2 rounded text-sm bg-white">
                            @foreach(\App\Enums\InclusiveRadar\ConservationState::cases() as $state)
                                <option value="{{ $state->value }}" {{ old('conservation_state', $material->conservation_state?->value) == $state->value ? 'selected' : '' }}>
                                    {{ $state->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1 text-[10px] uppercase">Tipo do Registro</label>
                        <select name="inspection_type" class="w-full border p-2 rounded text-sm bg-white">
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
                        <label class="block font-bold text-gray-700 mb-1 text-[10px] uppercase">Data</label>
                        <input type="date" name="inspection_date" value="{{ date('Y-m-d') }}" class="w-full border p-2 rounded text-sm">
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block font-bold text-gray-700 mb-1 text-[10px] uppercase">Novas Fotos (Opcional)</label>
                    <input type="file" name="images[]" multiple accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-xs file:font-bold file:bg-blue-600 file:text-white cursor-pointer">
                </div>
                <div class="mt-4">
                    <label class="block font-bold text-gray-700 mb-1 text-[10px] uppercase">Motivo da Alteração</label>
                    <textarea name="inspection_description" rows="1" class="w-full border p-2 rounded text-sm focus:ring-2 focus:ring-blue-500" placeholder="Ex: Manutenção periódica, danos detectados..."></textarea>
                </div>
            </div>

            {{-- ESPECIFICAÇÕES E ACESSIBILIDADE --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div id="dynamic-attributes-container" class="hidden">
                    <label class="block font-bold text-gray-800 mb-2 border-l-4 border-blue-500 pl-2 text-xs uppercase">Especificações Técnicas</label>
                    <div id="dynamic-attributes" class="grid grid-cols-1 gap-4 bg-gray-50 p-4 rounded-lg border"></div>
                </div>
                <div>
                    <label class="block font-bold text-gray-800 mb-2 border-l-4 border-purple-500 pl-2 text-xs uppercase">Recursos de Acessibilidade</label>
                    <div class="border p-4 rounded bg-gray-50 max-h-48 overflow-y-auto grid grid-cols-1 gap-2">
                        @foreach(\App\Models\InclusiveRadar\AccessibilityFeature::where('is_active', true)->get() as $feature)
                            <div class="flex items-center gap-2">
                                <input type="checkbox" name="accessibility_features[]" value="{{ $feature->id }}" id="feat_{{ $feature->id }}"
                                       {{ in_array($feature->id, old('accessibility_features', $material->accessibilityFeatures->pluck('id')->toArray())) ? 'checked' : '' }}
                                       class="w-4 h-4 text-purple-600 rounded">
                                <label for="feat_{{ $feature->id }}" class="text-xs cursor-pointer text-gray-700">{{ $feature->name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- PÚBLICO E INVENTÁRIO --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block font-bold text-gray-700 mb-2 text-[10px] uppercase">Público-alvo *</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 bg-gray-50 p-4 rounded border">
                        @foreach(\App\Models\SpecializedEducationalSupport\Deficiency::where('is_active', true)->get() as $def)
                            <div class="flex items-center gap-2">
                                <input type="checkbox" name="deficiencies[]" value="{{ $def->id }}" id="def_{{ $def->id }}"
                                       {{ in_array($def->id, old('deficiencies', $material->deficiencies->pluck('id')->toArray())) ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 rounded">
                                <label for="def_{{ $def->id }}" class="text-xs cursor-pointer text-gray-600">{{ $def->name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-4">
                    @php $activeLoans = $material->loans()->whereNull('return_date')->count(); @endphp
                    <div id="asset-code-container">
                        <label class="block font-bold text-gray-700 mb-1 text-[10px] uppercase">Cód. Patrimonial</label>
                        <input type="text" name="asset_code" value="{{ old('asset_code', $material->asset_code) }}" class="w-full border p-2 rounded text-sm">
                    </div>
                    <div id="quantity-container">
                        <label class="block font-bold text-gray-700 mb-1 text-[10px] uppercase">Quantidade Total *</label>
                        <input type="number" name="quantity" id="quantity_field" value="{{ old('quantity', $material->quantity) }}" min="{{ $activeLoans }}" class="w-full border p-2 rounded text-sm">
                        @if($activeLoans > 0)
                            <p class="text-[10px] text-amber-600 font-bold mt-1 italic"><i class="fas fa-info-circle"></i> {{ $activeLoans }} emprestado(s).</p>
                        @endif
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1 text-[10px] uppercase">Status Operacional</label>
                        <select name="status_id" class="w-full border p-2 rounded bg-white text-sm">
                            @foreach(\App\Models\InclusiveRadar\ResourceStatus::where('is_active', true)->get() as $status)
                                <option value="{{ $status->id }}" {{ old('status_id', $material->status_id) == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- BOTÕES --}}
            <div class="flex gap-4 mt-4 border-t pt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-10 py-3 rounded shadow-lg transition font-bold text-lg flex-1 md:flex-none">
                    <i class="fas fa-sync-alt mr-2"></i> Atualizar Registro
                </button>
                <a href="{{ route('inclusive-radar.accessible-educational-materials.index') }}" class="bg-gray-500 text-white px-6 py-3 rounded font-bold">Cancelar</a>
            </div>
        </div>
    </form>
</div>

<script>
    const typeSelect = document.getElementById('type_id');
    const container = document.getElementById('dynamic-attributes');
    const outerContainer = document.getElementById('dynamic-attributes-container');
    const assetContainer = document.getElementById('asset-code-container');
    const quantityContainer = document.getElementById('quantity-container');
    const quantityField = document.getElementById('quantity_field');

    const initialValues = @json($attributeValues ?? []);

    function handleTypeChange() {
        const isDigital = typeSelect.options[typeSelect.selectedIndex].getAttribute('data-digital') === '1';
        assetContainer.classList.toggle('hidden', isDigital);
        quantityContainer.classList.toggle('hidden', isDigital);
        if (isDigital) quantityField.value = '';
    }

    function loadAttributes(typeId, currentValues = {}) {
        if (!typeId) { outerContainer.classList.add('hidden'); return; }
        container.innerHTML = '<p class="text-xs text-gray-400 italic px-2">Sincronizando...</p>';
        outerContainer.classList.remove('hidden');

        fetch("{{ url('inclusive-radar/resource-types') }}/" + typeId + "/attributes")
            .then(res => res.json())
            .then(attributes => {
                container.innerHTML = '';
                if (attributes.length > 0) {
                    attributes.forEach(attr => {
                        const div = document.createElement('div');
                        div.className = "flex flex-col gap-1";
                        const val = currentValues[attr.id] || '';

                        let inputHTML = `<label class="text-[10px] font-bold text-gray-600 uppercase">${attr.label}</label>`;
                        if (attr.field_type === 'boolean') {
                            div.className = "flex items-center gap-3 p-2 bg-white rounded border border-gray-100 shadow-sm";
                            inputHTML = `<input type="hidden" name="attributes[${attr.id}]" value="0"><input type="checkbox" name="attributes[${attr.id}]" value="1" class="w-4 h-4 text-blue-600" ${val == '1' ? 'checked' : ''}><label class="text-xs font-bold text-gray-600">${attr.label}</label>`;
                        } else {
                            inputHTML += `<input type="${['integer','decimal'].includes(attr.field_type)?'number':(attr.field_type==='date'?'date':'text')}" name="attributes[${attr.id}]" value="${val}" class="w-full border p-2 rounded text-xs bg-white focus:ring-2 focus:ring-blue-500">`;
                        }
                        div.innerHTML = inputHTML;
                        container.appendChild(div);
                    });
                } else outerContainer.classList.add('hidden');
            });
    }

    typeSelect.addEventListener('change', () => { handleTypeChange(); loadAttributes(typeSelect.value, {}); });
    handleTypeChange();
    loadAttributes(typeSelect.value, initialValues);
</script>
</body>
</html>
