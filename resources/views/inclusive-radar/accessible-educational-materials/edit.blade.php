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
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow border-t-4 border-yellow-500">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h1 class="text-2xl font-bold text-gray-800">Editar Material: {{ $material->title }}</h1>
        <span class="text-sm text-gray-500">ID: #{{ $material->id }}</span>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded">
            <p class="font-bold mb-1 italic">Atenção: Existem erros no preenchimento.</p>
            <ul class="list-disc ml-5 text-sm">
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
                @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Imagens --}}
            <div class="bg-blue-50 p-4 rounded border border-blue-100">
                <label class="block font-semibold text-blue-800 mb-1">
                    <i class="fas fa-camera mr-1"></i> Adicionar Novas Imagens
                </label>
                <input type="file" name="images[]" multiple accept="image/*"
                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">

                @if($material->images->count() > 0)
                    <div class="mt-4 grid grid-cols-4 gap-2">
                        @foreach($material->images as $image)
                            <div class="relative group">
                                <img src="{{ asset('storage/' . $image->path) }}" class="h-20 w-full object-cover rounded border">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Categoria --}}
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Tipo / Categoria *</label>
                    <select name="type_id" id="type_id" class="w-full border p-2 rounded bg-white focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Selecione o tipo --</option>
                        @foreach(\App\Models\InclusiveRadar\ResourceType::where('for_educational_material', true)->where('is_active', true)->get() as $type)
                            <option value="{{ $type->id }}" {{ old('type_id', $material->type_id) == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Status --}}
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Status Operacional</label>
                    <select name="status_id" class="w-full border p-2 rounded bg-white focus:ring-2 focus:ring-blue-500">
                        <option value="">Selecione um status</option>
                        @foreach(\App\Models\InclusiveRadar\ResourceStatus::where('is_active', true)->get() as $status)
                            <option value="{{ $status->id }}" {{ old('status_id', $material->status_id) == $status->id ? 'selected' : '' }}>
                                {{ $status->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Recursos de Acessibilidade --}}
            <div class="mt-4">
                <label class="block font-bold text-gray-700 mb-2">Recursos de Acessibilidade Presentes *</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2 bg-purple-50 p-4 rounded border border-purple-200">
                    @php
                        $selectedFeatures = collect(old('accessibility_features', $material->accessibilityFeatures->pluck('id')->toArray()))->map(fn($id) => (int)$id)->toArray();
                        $features = \App\Models\InclusiveRadar\AccessibilityFeature::where('is_active', true)->get();
                    @endphp
                    @foreach($features as $feature)
                        <div class="flex items-center gap-2 group">
                            <input type="checkbox" name="accessibility_features[]" value="{{ $feature->id }}" id="feat_{{ $feature->id }}"
                                   {{ in_array($feature->id, $selectedFeatures) ? 'checked' : '' }}
                                   class="w-4 h-4 text-purple-600 rounded border-gray-300">
                            <label for="feat_{{ $feature->id }}" class="text-sm cursor-pointer">{{ $feature->name }}</label>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Atributos Dinâmicos --}}
            <div id="dynamic-attributes-container" class="{{ $material->type_id ? '' : 'hidden' }} mt-4">
                <label class="block font-bold text-blue-900 mb-2 border-l-4 border-blue-500 pl-2">Especificações Técnicas</label>
                <div id="dynamic-attributes" class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200"></div>
            </div>

            {{-- Público-alvo --}}
            <div class="mt-4">
                <label class="block font-bold text-gray-700 mb-2">Público-alvo (Deficiências) *</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2 bg-gray-50 p-4 rounded border border-gray-200">
                    @php
                        $selectedDeficiencies = old('deficiencies', $material->deficiencies->pluck('id')->toArray());
                    @endphp
                    @foreach(\App\Models\SpecializedEducationalSupport\Deficiency::where('is_active', true)->get() as $def)
                        <div class="flex items-center gap-2 group">
                            <input type="checkbox" name="deficiencies[]" value="{{ $def->id }}" id="def_{{ $def->id }}"
                                   {{ in_array($def->id, $selectedDeficiencies) ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 rounded border-gray-300">
                            <label for="def_{{ $def->id }}" class="text-sm cursor-pointer">{{ $def->name }}</label>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Código Patrimonial</label>
                    <input type="text" name="asset_code" value="{{ old('asset_code', $material->asset_code) }}" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Observações / Notas</label>
                    <textarea name="notes" rows="1" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500">{{ old('notes', $material->notes) }}</textarea>
                </div>
            </div>

            {{-- Configurações Ativo/Treinamento (Padronizado com TA) --}}
            <div class="flex flex-col gap-2 p-3 bg-gray-100 rounded border border-gray-300 mt-2">
                <div class="flex items-center gap-2">
                    {{-- Importante: hidden antes do checkbox --}}
                    <input type="hidden" name="requires_training" value="0">
                    <input type="checkbox" name="requires_training" id="requires_training" value="1"
                           {{ old('requires_training', $material->requires_training) ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 rounded">
                    <label for="requires_training" class="cursor-pointer text-sm font-medium text-gray-700">Requer Treinamento para o uso</label>
                </div>
                <div class="flex items-center gap-2">
                    {{-- Importante: hidden antes do checkbox --}}
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                           {{ old('is_active', $material->is_active) ? 'checked' : '' }}
                           class="w-4 h-4 text-green-600 rounded">
                    <label for="is_active" class="cursor-pointer text-sm font-bold text-green-700">Material com Cadastro Ativo</label>
                </div>
            </div>

            <hr class="my-4">

            <div class="flex gap-4">
                <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-10 py-3 rounded shadow-lg transition font-bold text-lg flex-1 md:flex-none">
                    <i class="fas fa-sync mr-2"></i> Atualizar Material
                </button>
                <a href="{{ route('inclusive-radar.accessible-educational-materials.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded transition flex items-center font-bold">
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

    /**
     * Carrega atributos via AJAX e preenche valores salvos
     */
    function loadAttributes(typeId, currentValues = {}) {
        if (!typeId) {
            container.innerHTML = '';
            outerContainer.classList.add('hidden');
            return;
        }

        container.innerHTML = '<p class="text-sm text-gray-400">Carregando especificações...</p>';
        outerContainer.classList.remove('hidden');

        // Note que aqui usamos o prefixo /inclusive-radar/ conforme seu exemplo de TA que funciona
        fetch(`/inclusive-radar/resource-types/${typeId}/attributes`)
            .then(res => {
                if (!res.ok) throw new Error('Erro ao buscar atributos');
                return res.json();
            })
            .then(attributes => {
                container.innerHTML = '';
                if (attributes.length > 0) {
                    attributes.forEach(attr => {
                        const div = document.createElement('div');
                        div.className = "flex flex-col gap-1";

                        const label = document.createElement('label');
                        label.className = "text-sm font-bold text-gray-600";
                        label.innerText = attr.label + (attr.is_required ? ' *' : '');

                        // Recupera o valor salvo (Prioridade: ID do atributo)
                        const savedValue = currentValues[attr.id] || '';

                        let input;
                        if (attr.field_type === 'text') {
                            input = document.createElement('textarea');
                            input.rows = 2;
                        } else if (attr.field_type === 'boolean') {
                            div.className = "flex items-center gap-3 p-2 bg-white rounded border border-gray-100";
                            input = document.createElement('input');
                            input.type = 'checkbox';
                            input.className = "w-5 h-5 text-blue-600";
                            if (savedValue == '1' || savedValue === 'on') input.checked = true;
                        } else {
                            input = document.createElement('input');
                            input.type = (attr.field_type === 'integer' || attr.field_type === 'decimal') ? 'number' :
                                (attr.field_type === 'date' ? 'date' : 'text');
                            if(attr.field_type === 'decimal') input.step = '0.01';
                            input.value = savedValue;
                        }

                        if (attr.field_type !== 'boolean') {
                            input.className = 'w-full border p-2 rounded focus:ring-2 focus:ring-blue-500 text-sm';
                        }

                        input.name = `attributes[${attr.id}]`;

                        if(attr.field_type === 'boolean') {
                            div.appendChild(input);
                            div.appendChild(label);
                        } else {
                            div.appendChild(label);
                            div.appendChild(input);
                        }

                        container.appendChild(div);
                    });
                } else {
                    outerContainer.classList.add('hidden');
                }
            })
            .catch(err => {
                console.error("Erro:", err);
                container.innerHTML = '<p class="text-red-500 text-sm">Erro ao carregar especificações técnicas.</p>';
            });
    }

    // Lógica para capturar os valores que já existem no banco (Igual ao seu TA)
    @php
        $databaseValues = \App\Models\InclusiveRadar\ResourceAttributeValue::where('resource_type', 'educational_material')
            ->where('resource_id', $material->id)
            ->pluck('value', 'attribute_id');

        $finalValues = old('attributes', $databaseValues);
    @endphp

    const initialValues = @json($finalValues);

    // 1. Carrega ao abrir a página
    if (typeSelect.value) {
        loadAttributes(typeSelect.value, initialValues);
    }

    // 2. Evento de mudança
    typeSelect.addEventListener('change', function() {
        if(this.value == "{{ $material->type_id }}") {
            loadAttributes(this.value, initialValues);
        } else {
            loadAttributes(this.value, {});
        }
    });
</script>
</body>
</html>
