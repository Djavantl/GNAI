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
        <h1 class="text-2xl font-bold text-gray-800">Editar Tecnologia Assistiva</h1>
        <span class="text-sm text-gray-500 font-mono bg-gray-100 px-2 py-1 rounded">ID: #{{ $assistiveTechnology->id }}</span>
    </div>

    {{-- Exibição de Erros --}}
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

    <form action="{{ route('inclusive-radar.assistive-technologies.update', $assistiveTechnology->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-4">

            {{-- Nome --}}
            <div>
                <label class="block font-bold text-gray-700 mb-1">Nome da Tecnologia *</label>
                <input type="text" name="name"
                       value="{{ old('name', $assistiveTechnology->name) }}"
                       class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
            </div>

            {{-- Descrição --}}
            <div>
                <label class="block font-bold text-gray-700 mb-1">Descrição</label>
                <textarea name="description" rows="3"
                          class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500">{{ old('description', $assistiveTechnology->description) }}</textarea>
            </div>

            {{-- Imagens Atuais --}}
            @if($assistiveTechnology->images->count() > 0)
                <div class="mt-2">
                    <label class="block font-bold mb-2 text-gray-700">Imagens Atuais ({{ $assistiveTechnology->images->count() }})</label>
                    <div class="grid grid-cols-3 sm:grid-cols-5 gap-4 bg-gray-50 p-4 rounded border border-gray-200">
                        @foreach($assistiveTechnology->images as $image)
                            <div class="relative group border rounded p-1 bg-white shadow-sm">
                                <img src="{{ asset('storage/' . $image->path) }}" alt="Imagem"
                                     class="h-24 w-full object-contain rounded">
                                <button type="button"
                                        onclick="if(confirm('Deseja excluir esta imagem permanentemente?')) document.getElementById('delete-image-{{ $image->id }}').submit();"
                                        class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full p-1 shadow hover:bg-red-700 transition">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Adicionar novas imagens --}}
            <div class="bg-blue-50 p-4 rounded border border-blue-100 mt-2">
                <label class="block font-semibold text-blue-800 mb-1">
                    <i class="fas fa-plus-circle mr-1"></i> Adicionar Novas Imagens
                </label>
                <input type="file" name="images[]" multiple accept="image/*"
                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                <p class="text-xs text-blue-600 mt-1 italic">As novas fotos serão somadas à galeria atual.</p>
            </div>

            {{-- Tipo / Categoria --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Tipo / Categoria *</label>
                    <select name="type_id" id="type_id" class="w-full border p-2 rounded bg-white focus:ring-2 focus:ring-blue-500 @error('type_id') border-red-500 @enderror">
                        <option value="">Selecione um tipo</option>
                        @foreach(\App\Models\InclusiveRadar\ResourceType::where('for_assistive_technology', true)->get() as $type)
                            <option value="{{ $type->id }}"
                                {{ old('type_id', $assistiveTechnology->type_id) == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                {{-- O campo Quantidade foi removido conforme sua migration --}}
            </div>

            {{-- SEÇÃO DE ATRIBUTOS DINÂMICOS --}}
            <div id="dynamic-attributes-container" class="hidden mt-4">
                <label class="block font-bold text-blue-900 mb-2 border-l-4 border-blue-500 pl-2">Especificações Técnicas</label>
                <div id="dynamic-attributes" class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                    {{-- Preenchido via JS --}}
                </div>
            </div>

            {{-- Público-alvo (Deficiências) --}}
            <div class="mt-4">
                <label class="block font-bold text-gray-700 mb-2">Público-alvo (Deficiências) *</label>
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

            {{-- Patrimônio, Estado e Status --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Patrimônio</label>
                    <input type="text" name="asset_code"
                           value="{{ old('asset_code', $assistiveTechnology->asset_code) }}"
                           class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Estado de Conservação</label>
                    <input type="text" name="conservation_state"
                           value="{{ old('conservation_state', $assistiveTechnology->conservation_state) }}"
                           class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Status Operacional</label>
                    <select name="status_id" class="w-full border p-2 rounded bg-white focus:ring-2 focus:ring-blue-500">
                        <option value="">Selecione</option>
                        @foreach(\App\Models\InclusiveRadar\ResourceStatus::where('is_active', true)
                            ->where('for_assistive_technology', true)
                            ->get() as $status)
                            <option value="{{ $status->id }}"
                                {{ old('status_id', $assistiveTechnology->status_id) == $status->id ? 'selected' : '' }}>
                                {{ $status->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Notas Internas --}}
            <div>
                <label class="block font-bold text-gray-700 mb-1">Notas Internas / Observações</label>
                <textarea name="notes" rows="2"
                          class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500">{{ old('notes', $assistiveTechnology->notes) }}</textarea>
            </div>

            {{-- Configurações --}}
            <div class="flex flex-col gap-2 p-3 bg-gray-50 rounded border border-gray-200 mt-2">
                <div class="flex items-center gap-2">
                    <input type="hidden" name="requires_training" value="0">
                    <input type="checkbox" name="requires_training" id="requires_training" value="1"
                           {{ old('requires_training', $assistiveTechnology->requires_training) ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 rounded">
                    <label for="requires_training" class="cursor-pointer text-sm text-gray-700 font-medium">Requer Treinamento</label>
                </div>
                <div class="flex items-center gap-2">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                           {{ old('is_active', $assistiveTechnology->is_active) ? 'checked' : '' }}
                           class="w-4 h-4 text-green-600 rounded">
                    <label for="is_active" class="cursor-pointer text-sm font-bold text-green-700">Cadastro Ativo</label>
                </div>
            </div>

            <hr class="my-4">

            <div class="flex gap-4">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-10 py-3 rounded shadow-lg transition font-bold text-lg flex-1 md:flex-none">
                    <i class="fas fa-sync-alt mr-2"></i> Atualizar Registro
                </button>
                <a href="{{ route('inclusive-radar.assistive-technologies.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded transition flex items-center font-bold">
                    Cancelar
                </a>
            </div>
        </div>
    </form>

    {{-- Formulários ocultos para deletar imagens --}}
    @foreach($assistiveTechnology->images as $image)
        <form id="delete-image-{{ $image->id }}"
              action="{{ route('inclusive-radar.assistive-technologies.images.destroy', $image->id) }}"
              method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endforeach
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

                        // Valor salvo: vindo do banco ou do old() após erro de validação
                        const savedValue = currentValues[attr.id] || '';

                        let input;

                        if (attr.field_type === 'text') {
                            input = document.createElement('textarea');
                            input.rows = 2;
                            input.value = savedValue;
                        }
                        else if (attr.field_type === 'boolean') {
                            div.className = "flex items-center gap-3 p-2 bg-white rounded border border-gray-100";

                            // 1. Hidden input para garantir que o valor '0' seja enviado se desmarcado
                            const hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = `attributes[${attr.id}]`;
                            hiddenInput.value = '0';
                            div.appendChild(hiddenInput);

                            // 2. Checkbox real
                            input = document.createElement('input');
                            input.type = 'checkbox';
                            input.value = '1';
                            input.className = "w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500";

                            // Se o valor salvo for '1', 'true' ou 'on', marcamos o checkbox
                            if (savedValue == '1' || savedValue === 'on' || savedValue === true) {
                                input.checked = true;
                            }
                        }
                        else {
                            input = document.createElement('input');
                            input.type = (attr.field_type === 'integer' || attr.field_type === 'decimal') ? 'number' :
                                (attr.field_type === 'date' ? 'date' : 'text');

                            if (attr.field_type === 'decimal') input.step = '0.01';
                            input.value = savedValue;
                        }

                        // Estilização para campos que não são checkbox
                        if (attr.field_type !== 'boolean') {
                            input.className = 'w-full border p-2 rounded focus:ring-2 focus:ring-blue-500 text-sm';
                        }

                        input.name = `attributes[${attr.id}]`;

                        // Ordem de inserção no HTML
                        if (attr.field_type === 'boolean') {
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
                outerContainer.classList.add('hidden');
            });
    }

    // Lógica para determinar os valores iniciais (Banco de dados + Old inputs)
    @php
        $databaseValues = \App\Models\InclusiveRadar\ResourceAttributeValue::where('resource_type', 'assistive_technology')
            ->where('resource_id', $assistiveTechnology->id)
            ->pluck('value', 'attribute_id');

        // O old('attributes') tem prioridade sobre o banco de dados
        $finalValues = old('attributes', $databaseValues);
    @endphp

    const initialValues = @json($finalValues);

    // 1. Carrega os atributos ao abrir a página usando o tipo atual da tecnologia
    if (typeSelect.value) {
        loadAttributes(typeSelect.value, initialValues);
    }

    // 2. Evento de mudança de tipo
    typeSelect.addEventListener('change', function() {
        // Se o usuário voltar para o tipo original da tecnologia, restauramos os valores salvos
        if (this.value == "{{ $assistiveTechnology->type_id }}") {
            loadAttributes(this.value, initialValues);
        } else {
            // Se mudar para um novo tipo, começa com os atributos vazios
            loadAttributes(this.value, {});
        }
    });
</script>
</body>
</html>
