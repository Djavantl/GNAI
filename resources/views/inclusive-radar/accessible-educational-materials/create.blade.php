<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Material Pedagógico Acessível - GNAI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-8">
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow border-t-4 border-blue-600">
    <h1 class="text-2xl font-bold mb-6 border-b pb-4 text-gray-800">Cadastrar Material Pedagógico Acessível (MPA)</h1>

    {{-- Bloco de Erros de Validação --}}
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

    {{-- ROTA: inclusive-radar.accessible-educational-materials.store --}}
    <form action="{{ route('inclusive-radar.accessible-educational-materials.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 gap-4">

            {{-- Título do Material --}}
            <div>
                <label class="block font-bold text-gray-700 mb-1">Título do Material *</label>
                <input type="text" name="title" value="{{ old('title') }}"
                       class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500 @error('title') border-red-500 @enderror"
                       placeholder="Ex: Livro em Braille, Maquete Tátil...">
                @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- SEÇÃO DE IMAGENS --}}
            <div class="bg-blue-50 p-4 rounded border border-blue-100">
                <label class="block font-semibold text-blue-800 mb-1">
                    <i class="fas fa-camera mr-1"></i> Imagens do Material
                </label>
                <input type="file" name="images[]" multiple accept="image/*"
                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Tipo / Categoria --}}
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Tipo / Categoria *</label>
                    <select name="type_id" id="type_id" class="w-full border p-2 rounded bg-white focus:ring-2 focus:ring-blue-500 @error('type_id') border-red-500 @enderror">
                        <option value="">-- Selecione o tipo --</option>
                        {{-- CORREÇÃO DA COLUNA: for_educational_material --}}
                        @foreach(\App\Models\InclusiveRadar\ResourceType::where('for_educational_material', true)->where('is_active', true)->get() as $type)
                            <option value="{{ $type->id }}" {{ old('type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('type_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                {{-- Status --}}
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Status Operacional</label>
                    <select name="status_id" class="w-full border p-2 rounded bg-white focus:ring-2 focus:ring-blue-500">
                        <option value="">Selecione um status</option>
                        @foreach(\App\Models\InclusiveRadar\ResourceStatus::where('is_active', true)->get() as $status)
                            <option value="{{ $status->id }}" {{ old('status_id') == $status->id ? 'selected' : '' }}>
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
                    @foreach(\App\Models\InclusiveRadar\AccessibilityFeature::where('is_active', true)->get() as $feature)
                        <div class="flex items-center gap-2 group">
                            <input type="checkbox" name="accessibility_features[]" value="{{ $feature->id }}" id="feat_{{ $feature->id }}"
                                   {{ (is_array(old('accessibility_features')) && in_array($feature->id, old('accessibility_features'))) ? 'checked' : '' }}
                                   class="w-4 h-4 text-purple-600 rounded border-gray-300 focus:ring-purple-500">
                            <label for="feat_{{ $feature->id }}" class="text-sm cursor-pointer group-hover:text-purple-700">{{ $feature->name }}</label>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- SEÇÃO DE ATRIBUTOS DINÂMICOS --}}
            <div id="dynamic-attributes-container" class="hidden mt-4">
                <label class="block font-bold text-blue-900 mb-2 border-l-4 border-blue-500 pl-2">Especificações Técnicas</label>
                <div id="dynamic-attributes" class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200"></div>
            </div>

            {{-- Público-alvo (Deficiências) --}}
            <div class="mt-4">
                <label class="block font-bold text-gray-700 mb-2">Público-alvo (Deficiências) *</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2 bg-gray-50 p-4 rounded border border-gray-200">
                    @foreach(\App\Models\SpecializedEducationalSupport\Deficiency::where('is_active', true)->get() as $def)
                        <div class="flex items-center gap-2 group">
                            <input type="checkbox" name="deficiencies[]" value="{{ $def->id }}" id="def_{{ $def->id }}"
                                   {{ (is_array(old('deficiencies')) && in_array($def->id, old('deficiencies'))) ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 rounded border-gray-300">
                            <label for="def_{{ $def->id }}" class="text-sm cursor-pointer group-hover:text-blue-700">{{ $def->name }}</label>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Código Patrimonial</label>
                    <input type="text" name="asset_code" value="{{ old('asset_code') }}" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Observações / Notas</label>
                    <textarea name="notes" rows="1" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500">{{ old('notes') }}</textarea>
                </div>
            </div>

            {{-- Configurações de Ativo e Treinamento (Padronizado com TA) --}}
            <div class="flex flex-col gap-2 p-3 bg-gray-100 rounded border border-gray-300 mt-2">
                <div class="flex items-center gap-2">
                    <input type="hidden" name="requires_training" value="0">
                    <input type="checkbox" name="requires_training" id="requires_training" value="1"
                           {{ old('requires_training') ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 rounded">
                    <label for="requires_training" class="cursor-pointer text-sm font-medium text-gray-700">Requer Treinamento para o uso</label>
                </div>
                <div class="flex items-center gap-2">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                           {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                           class="w-4 h-4 text-green-600 rounded">
                    <label for="is_active" class="cursor-pointer text-sm font-bold text-green-700">Material com Cadastro Ativo</label>
                </div>
            </div>

            <hr class="my-4">

            {{-- Botões de Ação --}}
            <div class="flex gap-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-10 py-3 rounded shadow-lg transition font-bold text-lg flex-1 md:flex-none">
                    <i class="fas fa-save mr-2"></i> Salvar Material
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

    // Recupera valores digitados anteriormente em caso de erro de validação
    const oldValues = @json(old('attributes', []));

    function loadAttributes(typeId, currentValues = {}) {
        if (!typeId) {
            outerContainer.classList.add('hidden');
            return;
        }

        container.innerHTML = '<p class="text-sm text-gray-500">Carregando especificações...</p>';
        outerContainer.classList.remove('hidden');

        fetch(`/inclusive-radar/resource-types/${typeId}/attributes`)
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

                        // Pega o valor que estava no 'old' se existir
                        const savedValue = currentValues[attr.id] || '';

                        let input;
                        if (attr.field_type === 'text') {
                            input = document.createElement('textarea');
                            input.rows = 2;
                        } else if (attr.field_type === 'boolean') {
                            // Estilização igual ao seu TA
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

                        // Ordem de renderização para checkbox
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
                console.error("Erro ao carregar atributos:", err);
                container.innerHTML = '<p class="text-red-500 text-sm">Erro ao carregar especificações.</p>';
            });
    }

    typeSelect.addEventListener('change', function() {
        // Ao mudar manualmente, passamos um objeto vazio para limpar os campos
        loadAttributes(this.value, {});
    });

    // Inicialização (importante para erros de validação)
    if (typeSelect.value) {
        loadAttributes(typeSelect.value, oldValues);
    }
</script>
</body>
</html>
