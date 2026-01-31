<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tecnologia Assistiva - GNAI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-gray-100 p-8">
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow border-t-4 border-blue-600">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-bold text-gray-800">Editar Tecnologia Assistiva</h1>
            @if(!$assistiveTechnology->is_active)
                <span class="bg-amber-100 text-amber-700 text-[10px] font-bold px-2 py-1 rounded border border-amber-200 uppercase tracking-wider">Oculto no Catálogo</span>
            @endif
        </div>
        <span class="text-sm text-gray-500 font-mono bg-gray-100 px-2 py-1 rounded">ID: #{{ $assistiveTechnology->id }}</span>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded shadow-sm">
            <p class="font-bold mb-1 italic text-sm">Atenção: Existem erros no preenchimento.</p>
            <ul class="list-disc ml-5 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('inclusive-radar.assistive-technologies.update', $assistiveTechnology->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-4">

            {{-- Nome --}}
            <div>
                <label class="block font-bold text-gray-700 mb-1">Nome da Tecnologia / Equipamento *</label>
                <input type="text" name="name"
                       value="{{ old('name', $assistiveTechnology->name) }}"
                       class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
            </div>

            {{-- Descrição --}}
            <div>
                <label class="block font-bold text-gray-700 mb-1">Descrição Detalhada</label>
                <textarea name="description" rows="3"
                          class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500">{{ old('description', $assistiveTechnology->description) }}</textarea>
            </div>

            {{-- Imagens Atuais --}}
            @if($assistiveTechnology->images->count() > 0)
                <div class="mt-2">
                    <label class="block font-bold mb-2 text-gray-700"><i class="fas fa-images mr-1 text-blue-600"></i> Imagens Atuais ({{ $assistiveTechnology->images->count() }})</label>
                    <div class="grid grid-cols-3 sm:grid-cols-5 gap-4 bg-gray-50 p-4 rounded border border-gray-200">
                        @foreach($assistiveTechnology->images as $image)
                            <div class="relative group border rounded p-1 bg-white shadow-sm">
                                <img src="{{ asset('storage/' . $image->path) }}" alt="Imagem"
                                     class="h-24 w-full object-contain rounded transition group-hover:opacity-75">
                                <button type="button"
                                        class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full p-1 shadow-md hover:bg-red-700 transition">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Novas Imagens --}}
            <div class="bg-blue-50 p-4 rounded border border-blue-100 mt-2">
                <label class="block font-semibold text-blue-800 mb-1">
                    <i class="fas fa-plus-circle mr-1"></i> Adicionar Novas Imagens
                </label>
                <input type="file" name="images[]" multiple accept="image/*"
                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
            </div>

            {{-- Tipo / Categoria --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 mb-1 text-sm uppercase">Tipo / Categoria *</label>
                    <select name="type_id" id="type_id" class="w-full border p-2 rounded bg-white focus:ring-2 focus:ring-blue-500">
                        <option value="">Selecione um tipo</option>
                        @foreach(\App\Models\InclusiveRadar\ResourceType::where('for_assistive_technology', true)->where('is_active', true)->get() as $type)
                            <option value="{{ $type->id }}"
                                    data-digital="{{ $type->is_digital ? '1' : '0' }}"
                                {{ old('type_id', $assistiveTechnology->type_id) == $type->id ? 'selected' : '' }}>
                                {{ $type->name }} {{ $type->is_digital ? '(Digital)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- SEÇÃO DE ATRIBUTOS DINÂMICOS --}}
            <div id="dynamic-attributes-container" class="hidden mt-4">
                <label class="block font-bold text-blue-900 mb-2 border-l-4 border-blue-500 pl-2 text-sm uppercase">Especificações Técnicas</label>
                <div id="dynamic-attributes" class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                </div>
            </div>

            {{-- Público-alvo --}}
            <div class="mt-4">
                <label class="block font-bold text-gray-700 mb-2 text-sm uppercase">Público-alvo (Deficiências) *</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2 bg-gray-50 p-4 rounded border border-gray-200">
                    @foreach(\App\Models\SpecializedEducationalSupport\Deficiency::where('is_active', true)->get() as $def)
                        <div class="flex items-center gap-2 group">
                            <input type="checkbox" name="deficiencies[]" value="{{ $def->id }}" id="def_{{ $def->id }}"
                                   {{ in_array($def->id, old('deficiencies', $assistiveTechnology->deficiencies->pluck('id')->toArray())) ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                            <label for="def_{{ $def->id }}" class="text-sm cursor-pointer group-hover:text-blue-700 transition">{{ $def->name }}</label>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- SEÇÃO DE INVENTÁRIO --}}
            @php
                $isInitialDigital = $assistiveTechnology->type?->is_digital;
                $activeLoans = $assistiveTechnology->loans()->whereIn('status', ['active', 'late'])->count();
            @endphp

            <div id="inventory-section" class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-2">
                <div id="asset-code-container" class="{{ $isInitialDigital ? 'hidden' : '' }}">
                    <label class="block font-bold text-gray-700 mb-1">Patrimônio / Código</label>
                    <input type="text" name="asset_code"
                           value="{{ old('asset_code', $assistiveTechnology->asset_code) }}"
                           class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500">
                </div>

                <div id="quantity-container" class="{{ $isInitialDigital ? 'hidden' : '' }}">
                    <label class="block font-bold text-gray-700 mb-1">Quantidade Total *</label>
                    <input type="number" name="quantity" id="quantity_field"
                           value="{{ old('quantity', $assistiveTechnology->quantity) }}"
                           min="{{ $activeLoans }}"
                           class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500 @error('quantity') border-red-500 @enderror">

                    <div class="mt-2 p-2 bg-blue-50 border border-blue-100 rounded">
                        <p class="text-[10px] text-blue-700 leading-tight">
                            <strong>Saldo Disponível:</strong> {{ $assistiveTechnology->quantity_available }} unidades
                        </p>
                        @if($activeLoans > 0)
                            <p class="text-[10px] text-amber-600 font-bold italic mt-1 leading-tight">
                                <i class="fas fa-lock mr-1"></i> {{ $activeLoans }} unidades em empréstimo ativo.
                            </p>
                        @endif
                    </div>
                </div>

                <div id="conservation-container" class="{{ $isInitialDigital ? 'hidden' : '' }}">
                    <label class="block font-bold text-gray-700 mb-1">Estado de Conservação</label>
                    <input type="text" name="conservation_state"
                           value="{{ old('conservation_state', $assistiveTechnology->conservation_state) }}"
                           class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Status Operacional</label>
                    <select name="status_id" class="w-full border p-2 rounded bg-white focus:ring-2 focus:ring-blue-500">
                        <option value="">Selecione</option>
                        @foreach(\App\Models\InclusiveRadar\ResourceStatus::where('is_active', true)->where('for_assistive_technology', true)->get() as $status)
                            <option value="{{ $status->id }}"
                                {{ old('status_id', $assistiveTechnology->status_id) == $status->id ? 'selected' : '' }}>
                                {{ $status->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-2">
                <label class="block font-bold text-gray-700 mb-1">Notas Internas / Observações</label>
                <textarea name="notes" rows="2"
                          class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500 text-sm"
                          placeholder="Informações restritas à equipe de gestão...">{{ old('notes', $assistiveTechnology->notes) }}</textarea>
            </div>

            <div class="flex flex-col gap-2 p-3 bg-gray-100 rounded border border-gray-300 mt-2 shadow-sm">
                <div class="flex items-center gap-2">
                    <input type="hidden" name="requires_training" value="0">
                    <input type="checkbox" name="requires_training" id="requires_training" value="1"
                           {{ old('requires_training', $assistiveTechnology->requires_training) ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 rounded">
                    <label for="requires_training" class="cursor-pointer text-sm text-gray-700 font-medium italic">Requer Treinamento para o uso</label>
                </div>
                <div class="flex items-center gap-2">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                           {{ old('is_active', $assistiveTechnology->is_active) ? 'checked' : '' }}
                           class="w-4 h-4 text-green-600 rounded">
                    <label for="is_active" class="cursor-pointer text-sm font-bold text-green-700">Equipamento com Cadastro Ativo</label>
                </div>
            </div>

            <hr class="my-4 border-gray-200">

            <div class="flex flex-col md:flex-row gap-4">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-10 py-3 rounded shadow-lg transition font-bold text-lg flex-1 md:flex-none">
                    <i class="fas fa-sync-alt mr-2"></i> Atualizar Registro
                </button>
                <a href="{{ route('inclusive-radar.assistive-technologies.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded transition flex items-center justify-center font-bold text-lg">
                    Cancelar
                </a>
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

    function handleDigitalType() {
        const selectedOption = typeSelect.options[typeSelect.selectedIndex];
        if (!selectedOption || selectedOption.value === "") return;

        const isDigital = selectedOption.getAttribute('data-digital') === '1';
        if (isDigital) {
            assetContainer.classList.add('hidden');
            quantityContainer.classList.add('hidden');
            conservationContainer.classList.add('hidden');
            quantityField.value = '';
        } else {
            assetContainer.classList.remove('hidden');
            quantityContainer.classList.remove('hidden');
            conservationContainer.classList.remove('hidden');
        }
    }

    function loadAttributes(typeId, currentValues = {}) {
        if (!typeId) {
            container.innerHTML = '';
            outerContainer.classList.add('hidden');
            return;
        }

        container.innerHTML = '<p class="text-sm text-gray-500 italic px-2"><i class="fas fa-spinner fa-spin mr-2"></i>Sincronizando especificações...</p>';
        outerContainer.classList.remove('hidden');

        fetch("{{ url('inclusive-radar/resource-types') }}/" + typeId + "/attributes")
            .then(res => res.json())
            .then(attributes => {
                container.innerHTML = '';
                if (attributes.length > 0) {
                    attributes.forEach(attr => {
                        const div = document.createElement('div');
                        div.className = "flex flex-col gap-1";
                        const label = document.createElement('label');
                        label.className = "text-sm font-bold text-gray-600";
                        label.innerText = attr.label + (attr.is_required ? ' *' : '');

                        const savedValue = currentValues[attr.id] || '';
                        let input;

                        if (attr.field_type === 'text') {
                            input = document.createElement('textarea');
                            input.rows = 2; input.value = savedValue;
                        } else if (attr.field_type === 'boolean') {
                            div.className = "flex items-center gap-3 p-2 bg-white rounded border border-gray-100 shadow-sm";
                            const hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = `attributes[${attr.id}]`;
                            hiddenInput.value = '0';
                            div.appendChild(hiddenInput);
                            input = document.createElement('input');
                            input.type = 'checkbox';
                            input.value = '1';
                            input.className = "w-5 h-5 text-blue-600 rounded border-gray-300";
                            if (savedValue == '1' || savedValue === 'on' || savedValue === true) input.checked = true;
                        } else {
                            input = document.createElement('input');
                            input.type = (attr.field_type === 'integer' || attr.field_type === 'decimal') ? 'number' : (attr.field_type === 'date' ? 'date' : 'text');
                            if (attr.field_type === 'decimal') input.step = '0.01';
                            input.value = savedValue;
                        }

                        if (attr.field_type !== 'boolean') {
                            input.className = 'w-full border p-2 rounded focus:ring-2 focus:ring-blue-500 text-sm bg-white';
                        }
                        input.name = `attributes[${attr.id}]`;
                        if (attr.field_type === 'boolean') { div.appendChild(input); div.appendChild(label); }
                        else { div.appendChild(label); div.appendChild(input); }
                        container.appendChild(div);
                    });
                } else { outerContainer.classList.add('hidden'); }
            });
    }

    @php
        $dbValues = \App\Models\InclusiveRadar\ResourceAttributeValue::where('resource_type', 'assistive_technology')
            ->where('resource_id', $assistiveTechnology->id)
            ->pluck('value', 'attribute_id');
        $finalValues = old('attributes', $dbValues);
    @endphp

    const initialValues = @json($finalValues);

    if (typeSelect.value) {
        handleDigitalType();
        loadAttributes(typeSelect.value, initialValues);
    }

    typeSelect.addEventListener('change', function() {
        handleDigitalType();
        loadAttributes(this.value, {});
    });
</script>
</body>
</html>
