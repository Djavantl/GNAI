<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Material Pedagógico Acessível - GNAI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-4 md:p-8">
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow border-t-4 border-blue-600">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h1 class="text-2xl font-bold text-gray-800">Cadastrar Material Pedagógico (MPA)</h1>
        <span class="bg-blue-100 text-blue-800 text-xs font-bold px-3 py-1 rounded-full">NOVO RECURSO</span>
    </div>

    {{-- Alertas de Erro --}}
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded shadow-sm text-sm">
            <p class="font-bold mb-1 italic">Atenção: Verifique os campos abaixo.</p>
            <ul class="list-disc ml-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('inclusive-radar.accessible-educational-materials.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 gap-6">

            {{-- SEÇÃO 1: IDENTIFICAÇÃO BÁSICA --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="block font-bold text-gray-700 mb-1 text-sm uppercase">Título do Material *</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                           placeholder="Ex: Livro em Braille, Maquete Tátil...">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1 text-sm uppercase">Tipo / Categoria *</label>
                    <select name="type_id" id="type_id" class="w-full border p-2 rounded bg-white focus:ring-2 focus:ring-blue-500 @error('type_id') border-red-500 @enderror">
                        <option value="">-- Selecione --</option>
                        @foreach($resourceTypes as $type)
                            <option value="{{ $type->id }}"
                                    data-digital="{{ $type->is_digital ? '1' : '0' }}"
                                {{ old('type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- SEÇÃO 2: VISTORIA INICIAL (Sincronizado com TA) --}}
            <div class="bg-blue-50 p-5 rounded-lg border border-blue-200 shadow-inner">
                <h3 class="text-blue-800 font-bold mb-4 flex items-center gap-2 uppercase text-xs">
                    <i class="fas fa-clipboard-check"></i> Registro de Vistoria Inicial
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div id="conservation-container" class="hidden">
                        <label class="block font-bold text-gray-700 mb-1 text-xs uppercase">Estado de Conservação *</label>
                        <select name="conservation_state" class="w-full border p-2 rounded bg-white focus:ring-2 focus:ring-blue-500">
                            @foreach(\App\Enums\InclusiveRadar\ConservationState::cases() as $state)
                                <option value="{{ $state->value }}" {{ old('conservation_state') == $state->value ? 'selected' : '' }}>
                                    {{ $state->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1 text-xs uppercase">Fotos da Vistoria</label>
                        <input type="file" name="images[]" multiple accept="image/*"
                               class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-xs file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block font-bold text-gray-700 mb-1 text-xs uppercase">Observações da Vistoria</label>
                    <textarea name="inspection_description" rows="2" class="w-full border p-2 rounded text-sm focus:ring-2 focus:ring-blue-500"
                              placeholder="Descreva o estado do material na chegada...">{{ old('inspection_description') }}</textarea>
                </div>
            </div>

            {{-- SEÇÃO 3: ESPECIFICAÇÕES E ACESSIBILIDADE --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div id="dynamic-attributes-container" class="hidden">
                    <label class="block font-bold text-gray-800 mb-2 border-l-4 border-blue-500 pl-2 text-sm uppercase">Especificações Técnicas</label>
                    <div id="dynamic-attributes" class="grid grid-cols-1 gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm"></div>
                </div>

                <div>
                    <label class="block font-bold text-gray-800 mb-2 border-l-4 border-purple-500 pl-2 text-sm uppercase">Recursos de Acessibilidade</label>
                    <div class="border p-4 rounded bg-gray-50 max-h-48 overflow-y-auto grid grid-cols-1 gap-2">
                        @foreach(\App\Models\InclusiveRadar\AccessibilityFeature::where('is_active', true)->get() as $feature)
                            <div class="flex items-center gap-2">
                                <input type="checkbox" name="accessibility_features[]" value="{{ $feature->id }}" id="feat_{{ $feature->id }}"
                                       {{ collect(old('accessibility_features'))->contains($feature->id) ? 'checked' : '' }}
                                       class="w-4 h-4 text-purple-600 rounded">
                                <label for="feat_{{ $feature->id }}" class="text-sm cursor-pointer text-gray-700">{{ $feature->name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- SEÇÃO 4: PÚBLICO E INVENTÁRIO --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block font-bold text-gray-700 mb-2 text-sm uppercase">Público-alvo / Deficiências *</label>
                    <div class="grid grid-cols-1 gap-2 bg-gray-50 p-4 rounded border border-gray-200 max-h-48 overflow-y-auto shadow-inner">
                        @foreach($deficiencies as $def)
                            <div class="flex items-center gap-2">
                                <input type="checkbox" name="deficiencies[]" value="{{ $def->id }}" id="def_{{ $def->id }}"
                                       {{ collect(old('deficiencies'))->contains($def->id) ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 rounded">
                                <label for="def_{{ $def->id }}" class="text-xs cursor-pointer text-gray-600 font-medium">{{ $def->name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-4">
                    <div id="inventory-section" class="grid grid-cols-1 gap-4">
                        <div id="asset-code-container" class="hidden">
                            <label class="block font-bold text-gray-700 mb-1 text-xs uppercase">Cód. Patrimonial</label>
                            <input type="text" name="asset_code" value="{{ old('asset_code') }}" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div id="quantity-container" class="hidden">
                            <label class="block font-bold text-gray-700 mb-1 text-xs uppercase">Quantidade Total *</label>
                            <input type="number" name="quantity" id="quantity_field" value="{{ old('quantity', 1) }}" min="0" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 mb-1 text-xs uppercase">Status Operacional</label>
                            <select name="status_id" class="w-full border p-2 rounded bg-white focus:ring-2 focus:ring-blue-500">
                                <option value="">Selecione...</option>
                                @foreach(\App\Models\InclusiveRadar\ResourceStatus::where('is_active', true)->where('for_educational_material', true)->get() as $status)
                                    <option value="{{ $status->id }}" {{ old('status_id') == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- OPÇÕES FINAIS --}}
            <div class="flex flex-col gap-2 p-4 bg-gray-50 rounded border border-gray-300">
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="requires_training" id="requires_training" value="1" {{ old('requires_training') ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded">
                    <label for="requires_training" class="cursor-pointer text-sm font-medium text-gray-700 italic">Requer treinamento específico para uso</label>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} class="w-4 h-4 text-green-600 rounded">
                    <label for="is_active" class="cursor-pointer text-sm font-bold text-green-700">Material ativo para empréstimos</label>
                </div>
            </div>

            {{-- BOTÕES --}}
            <div class="flex gap-4 mt-4 border-t pt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-10 py-3 rounded shadow-lg transition font-bold text-lg flex-1 md:flex-none">
                    <i class="fas fa-save mr-2"></i> Salvar Material
                </button>
                <a href="{{ route('inclusive-radar.accessible-educational-materials.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-6 py-3 rounded transition flex items-center font-bold border">Cancelar</a>
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
    const conservationContainer = document.getElementById('conservation-container');
    const quantityField = document.getElementById('quantity_field');
    const oldAttributes = @json(old('attributes', []));

    function handleTypeChange() {
        const selectedOption = typeSelect.options[typeSelect.selectedIndex];
        if (!selectedOption || selectedOption.value === "") {
            [assetContainer, quantityContainer, conservationContainer].forEach(el => el.classList.add('hidden'));
            outerContainer.classList.add('hidden');
            return;
        }

        const isDigital = selectedOption.getAttribute('data-digital') === '1';
        assetContainer.classList.toggle('hidden', isDigital);
        quantityContainer.classList.toggle('hidden', isDigital);
        conservationContainer.classList.remove('hidden');

        if (isDigital) {
            quantityField.value = '';
        } else if (!quantityField.value) {
            quantityField.value = 1;
        }
    }

    function loadAttributes(typeId, currentValues = {}) {
        if (!typeId) {
            container.innerHTML = '';
            outerContainer.classList.add('hidden');
            return;
        }
        container.innerHTML = '<div class="col-span-full p-2 text-blue-600 text-xs italic"><i class="fas fa-spinner fa-spin mr-2"></i>Carregando...</div>';
        outerContainer.classList.remove('hidden');

        fetch("{{ url('inclusive-radar/resource-types') }}/" + typeId + "/attributes")
            .then(res => res.json())
            .then(attributes => {
                container.innerHTML = '';
                if (attributes && attributes.length > 0) {
                    attributes.forEach(attr => {
                        const div = document.createElement('div');
                        div.className = "flex flex-col gap-1";
                        const label = document.createElement('label');
                        label.className = "text-[11px] font-bold text-gray-600 uppercase";
                        label.innerText = attr.label + (attr.is_required ? ' *' : '');

                        const savedValue = currentValues[attr.id] || '';
                        let input;

                        if (attr.field_type === 'boolean') {
                            div.className = "flex items-center gap-3 p-2 bg-white rounded border border-gray-100 shadow-sm";
                            input = document.createElement('input');
                            input.type = 'checkbox';
                            input.name = `attributes[${attr.id}]`;
                            input.value = '1';
                            if (savedValue == '1') input.checked = true;
                            div.appendChild(input);
                            div.appendChild(label);
                        } else {
                            input = document.createElement('input');
                            input.type = (attr.field_type === 'integer' || attr.field_type === 'decimal') ? 'number' : 'text';
                            input.name = `attributes[${attr.id}]`;
                            input.value = savedValue;
                            input.className = 'w-full border p-2 rounded text-sm bg-white focus:ring-2 focus:ring-blue-500';
                            div.appendChild(label);
                            div.appendChild(input);
                        }
                        container.appendChild(div);
                    });
                } else { outerContainer.classList.add('hidden'); }
            });
    }

    typeSelect.addEventListener('change', function() {
        handleTypeChange();
        loadAttributes(this.value, {});
    });

    document.addEventListener('DOMContentLoaded', function() {
        if (typeSelect.value) {
            handleTypeChange();
            loadAttributes(typeSelect.value, oldAttributes);
        }
    });
</script>
</body>
</html>
