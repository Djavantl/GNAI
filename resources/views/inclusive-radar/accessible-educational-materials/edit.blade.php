<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Material Pedagógico Acessível - GNAI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-8">
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
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('inclusive-radar.accessible-educational-materials.update', $material->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-4">
            {{-- Título --}}
            <div>
                <label class="block font-bold text-gray-700 mb-1">Título do Material *</label>
                <input type="text" name="title" value="{{ old('title', $material->title) }}"
                       class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500 @error('title') border-red-500 @enderror">
            </div>

            {{-- Imagens Atuais --}}
            @if($material->images->count() > 0)
                <div class="mt-2">
                    <label class="block font-bold mb-2 text-gray-700"><i class="fas fa-images mr-1 text-blue-600"></i> Imagens Atuais ({{ $material->images->count() }})</label>
                    <div class="grid grid-cols-3 sm:grid-cols-5 gap-4 bg-gray-50 p-4 rounded border border-gray-200">
                        @foreach($material->images as $image)
                            <div class="relative group border rounded p-1 bg-white shadow-sm">
                                <img src="{{ asset('storage/' . $image->path) }}" class="h-24 w-full object-contain rounded">
                                {{-- Link para deletar imagem (ajuste a rota conforme seu sistema) --}}
                                <button type="button" class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full p-1 shadow-md hover:bg-red-700 transition">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Adicionar Novas Imagens --}}
            <div class="bg-blue-50 p-4 rounded border border-blue-100 mt-2">
                <label class="block font-semibold text-blue-800 mb-1 text-sm">
                    <i class="fas fa-plus-circle mr-1"></i> Adicionar Novas Imagens
                </label>
                <input type="file" name="images[]" multiple accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Tipo / Categoria --}}
                <div>
                    <label class="block font-bold text-gray-700 mb-1 text-sm uppercase">Tipo / Categoria *</label>
                    <select name="type_id" id="type_id" class="w-full border p-2 rounded bg-white focus:ring-2 focus:ring-blue-500">
                        @foreach(\App\Models\InclusiveRadar\ResourceType::where('for_educational_material', true)->where('is_active', true)->get() as $type)
                            <option value="{{ $type->id }}"
                                    data-digital="{{ $type->is_digital ? '1' : '0' }}"
                                {{ old('type_id', $material->type_id) == $type->id ? 'selected' : '' }}>
                                {{ $type->name }} {{ $type->is_digital ? '(Digital)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Recursos de Acessibilidade (Exclusivo MPA) --}}
                <div>
                    <label class="block font-bold text-gray-700 mb-1 text-sm uppercase">Acessibilidade</label>
                    <div class="border p-2 rounded bg-gray-50 max-h-32 overflow-y-auto">
                        @foreach(\App\Models\InclusiveRadar\AccessibilityFeature::where('is_active', true)->get() as $feature)
                            <div class="flex items-center gap-2 mb-1">
                                <input type="checkbox" name="accessibility_features[]" value="{{ $feature->id }}" id="feat_{{ $feature->id }}"
                                       {{ in_array($feature->id, old('accessibility_features', $material->accessibilityFeatures->pluck('id')->toArray())) ? 'checked' : '' }}
                                       class="w-4 h-4 text-purple-600 rounded">
                                <label for="feat_{{ $feature->id }}" class="text-xs cursor-pointer">{{ $feature->name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Atributos Dinâmicos --}}
            <div id="dynamic-attributes-container" class="hidden mt-2">
                <label class="block font-bold text-blue-900 mb-2 border-l-4 border-blue-500 pl-2 text-sm uppercase">Especificações Técnicas</label>
                <div id="dynamic-attributes" class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200"></div>
            </div>

            {{-- Público-alvo --}}
            <div class="mt-2">
                <label class="block font-bold text-gray-700 mb-2 text-sm uppercase">Público-alvo *</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2 bg-gray-50 p-4 rounded border border-gray-200">
                    @foreach(\App\Models\SpecializedEducationalSupport\Deficiency::where('is_active', true)->get() as $def)
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="deficiencies[]" value="{{ $def->id }}" id="def_{{ $def->id }}"
                                   {{ in_array($def->id, old('deficiencies', $material->deficiencies->pluck('id')->toArray())) ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 rounded">
                            <label for="def_{{ $def->id }}" class="text-sm cursor-pointer">{{ $def->name }}</label>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- INVENTÁRIO (IGUAL AO TA) --}}
            @php
                $isInitialDigital = $material->type?->is_digital;
                $activeLoans = $material->loans()->whereNull('return_date')->count();
            @endphp

            <div id="inventory-section" class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
                <div id="asset-code-container" class="{{ $isInitialDigital ? 'hidden' : '' }}">
                    <label class="block font-bold text-gray-700 mb-1">Cód. Patrimonial</label>
                    <input type="text" name="asset_code" value="{{ old('asset_code', $material->asset_code) }}" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500">
                </div>

                <div id="quantity-container" class="{{ $isInitialDigital ? 'hidden' : '' }}">
                    <label class="block font-bold text-gray-700 mb-1">Quantidade Total *</label>
                    <input type="number" name="quantity" id="quantity_field"
                           value="{{ old('quantity', $material->quantity) }}"
                           min="{{ $activeLoans }}"
                           class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500 @error('quantity') border-red-500 @enderror">

                    @if($activeLoans > 0)
                        <div class="mt-1 p-1 bg-amber-50 border border-amber-200 rounded text-[10px] text-amber-700 font-bold italic">
                            <i class="fas fa-lock mr-1"></i> {{ $activeLoans }} unidade(s) em empréstimo ativo.
                        </div>
                    @endif
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Status Operacional</label>
                    <select name="status_id" class="w-full border p-2 rounded bg-white focus:ring-2 focus:ring-blue-500">
                        @foreach(\App\Models\InclusiveRadar\ResourceStatus::where('is_active', true)->get() as $status)
                            <option value="{{ $status->id }}" {{ old('status_id', $material->status_id) == $status->id ? 'selected' : '' }}>
                                {{ $status->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Observações --}}
            <div class="mt-2">
                <label class="block font-bold text-gray-700 mb-1">Notas Internas</label>
                <textarea name="notes" rows="2" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500 text-sm">{{ old('notes', $material->notes) }}</textarea>
            </div>

            {{-- Checkboxes de Status --}}
            <div class="flex flex-col gap-2 p-3 bg-gray-100 rounded border border-gray-300 mt-2">
                <div class="flex items-center gap-2">
                    <input type="hidden" name="requires_training" value="0">
                    <input type="checkbox" name="requires_training" id="requires_training" value="1" {{ old('requires_training', $material->requires_training) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded">
                    <label for="requires_training" class="cursor-pointer text-sm font-medium text-gray-700 italic">Requer Treinamento</label>
                </div>
                <div class="flex items-center gap-2">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $material->is_active) ? 'checked' : '' }} class="w-4 h-4 text-green-600 rounded">
                    <label for="is_active" class="cursor-pointer text-sm font-bold text-green-700">Material Ativo</label>
                </div>
            </div>

            <div class="flex gap-4 mt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-10 py-3 rounded shadow-lg transition font-bold text-lg flex-1 md:flex-none">
                    <i class="fas fa-sync-alt mr-2"></i> Atualizar Material
                </button>
                <a href="{{ route('inclusive-radar.accessible-educational-materials.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded transition flex items-center font-bold">Cancelar</a>
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

    // Recupera valores do banco + merge com old()
    @php
        $dbValues = \App\Models\InclusiveRadar\ResourceAttributeValue::where('resource_type', 'accessible_educational_material')
            ->where('resource_id', $material->id)
            ->pluck('value', 'attribute_id');
        $initialValues = old('attributes', $dbValues);
    @endphp
    const initialValues = @json($initialValues);

    function handleDigitalType() {
        const selectedOption = typeSelect.options[typeSelect.selectedIndex];
        const isDigital = selectedOption.getAttribute('data-digital') === '1';

        if (isDigital) {
            assetContainer.classList.add('hidden');
            quantityContainer.classList.add('hidden');
            quantityField.value = '';
        } else {
            assetContainer.classList.remove('hidden');
            quantityContainer.classList.remove('hidden');
        }
    }

    function loadAttributes(typeId, currentValues = {}) {
        if (!typeId) { outerContainer.classList.add('hidden'); return; }

        container.innerHTML = '<p class="text-sm text-gray-500 italic px-2 col-span-2">Sincronizando...</p>';
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

                        if (attr.field_type === 'boolean') {
                            div.className = "flex items-center gap-3 p-2 bg-white rounded border border-gray-100 shadow-sm";
                            const hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = `attributes[${attr.id}]`;
                            hiddenInput.value = '0';
                            div.appendChild(hiddenInput);
                            input = document.createElement('input');
                            input.type = 'checkbox';
                            input.value = '1';
                            input.className = "w-5 h-5 text-blue-600 rounded";
                            if (savedValue == '1' || savedValue === 'on' || savedValue === true) input.checked = true;
                            div.appendChild(input);
                            div.appendChild(label);
                        } else {
                            input = document.createElement('input');
                            input.type = (attr.field_type === 'integer' || attr.field_type === 'decimal') ? 'number' : (attr.field_type === 'date' ? 'date' : 'text');
                            input.name = `attributes[${attr.id}]`;
                            input.value = savedValue;
                            input.className = 'w-full border p-2 rounded focus:ring-2 focus:ring-blue-500 text-sm bg-white';
                            div.appendChild(label);
                            div.appendChild(input);
                        }
                        container.appendChild(div);
                    });
                } else { outerContainer.classList.add('hidden'); }
            });
    }

    typeSelect.addEventListener('change', function() {
        handleDigitalType();
        loadAttributes(this.value, {});
    });

    // Inicia com os dados do banco
    if (typeSelect.value) {
        handleDigitalType();
        loadAttributes(typeSelect.value, initialValues);
    }
</script>
</body>
</html>
