<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Tecnologia Assistiva - GNAI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-4 md:p-8">
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow border-t-4 border-green-600">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h1 class="text-2xl font-bold text-gray-800">Cadastrar Tecnologia Assistiva</h1>
        <span class="bg-green-100 text-green-800 text-xs font-bold px-3 py-1 rounded-full">NOVO RECURSO</span>
    </div>

    {{-- Alertas de Erro --}}
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded shadow-sm">
            <p class="font-bold mb-1 italic text-sm">Atenção: Verifique os campos abaixo.</p>
            <ul class="list-disc ml-5 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('inclusive-radar.assistive-technologies.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Dados Automáticos da Inspeção Inicial via ENUM --}}
        <input type="hidden" name="inspection_type" value="{{ \App\Enums\InclusiveRadar\InspectionType::INITIAL->value }}">
        <input type="hidden" name="inspection_date" value="{{ date('Y-m-d') }}">

        <div class="grid grid-cols-1 gap-6">

            {{-- SEÇÃO 1: IDENTIFICAÇÃO BÁSICA --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="block font-bold text-gray-700 mb-1">Nome da Tecnologia / Equipamento *</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="w-full border p-2 rounded focus:ring-2 focus:ring-green-500 @error('name') border-red-500 @enderror"
                           placeholder="Ex: Teclado Adaptado, Mouse de Esfera...">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Tipo / Categoria *</label>
                    <select name="type_id" id="type_id" class="w-full border p-2 rounded bg-white focus:ring-2 focus:ring-green-500">
                        <option value="">-- Selecione --</option>
                        @foreach(\App\Models\InclusiveRadar\ResourceType::where('for_assistive_technology', true)->where('is_active', true)->get() as $type)
                            <option value="{{ $type->id }}"
                                    data-digital="{{ $type->is_digital ? '1' : '0' }}"
                                {{ old('type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1">Descrição do Recurso</label>
                <textarea name="description" rows="2" class="w-full border p-2 rounded focus:ring-2 focus:ring-green-500"
                          placeholder="Funcionalidades gerais do equipamento...">{{ old('description') }}</textarea>
            </div>

            {{-- SEÇÃO 2: VISTORIA INICIAL (DINÂMICA COM ENUM) --}}
            <div class="bg-blue-50 p-5 rounded-lg border border-blue-200 shadow-inner">
                <h3 class="text-blue-800 font-bold mb-4 flex items-center gap-2 uppercase text-sm">
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
                        <label class="block font-bold text-gray-700 mb-1 text-xs uppercase">Fotos do Equipamento *</label>
                        <input type="file" name="images[]" multiple accept="image/*" required
                               class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-xs file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block font-bold text-gray-700 mb-1 text-xs uppercase">Observações da Vistoria</label>
                    <textarea name="inspection_description" rows="2" class="w-full border p-2 rounded text-sm focus:ring-2 focus:ring-blue-500"
                              placeholder="Relate o estado físico na chegada do equipamento...">{{ old('inspection_description') }}</textarea>
                </div>
            </div>

            {{-- SEÇÃO 3: ATRIBUTOS DINÂMICOS (VIA AJAX) --}}
            <div id="dynamic-attributes-container" class="hidden">
                <label class="block font-bold text-gray-800 mb-2 border-l-4 border-blue-500 pl-2 text-sm uppercase">Especificações Técnicas</label>
                <div id="dynamic-attributes" class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm"></div>
            </div>

            {{-- SEÇÃO 4: PÚBLICO E INVENTÁRIO --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block font-bold text-gray-700 mb-2 text-sm uppercase">Público-alvo *</label>
                    <div class="grid grid-cols-2 gap-2 bg-gray-50 p-3 rounded border border-gray-200 h-full">
                        @foreach(\App\Models\SpecializedEducationalSupport\Deficiency::where('is_active', true)->get() as $def)
                            <div class="flex items-center gap-2">
                                <input type="checkbox" name="deficiencies[]" value="{{ $def->id }}" id="def_{{ $def->id }}"
                                       {{ (is_array(old('deficiencies')) && in_array($def->id, old('deficiencies'))) ? 'checked' : '' }}
                                       class="w-4 h-4 text-green-600 rounded focus:ring-green-500">
                                <label for="def_{{ $def->id }}" class="text-xs cursor-pointer text-gray-600">{{ $def->name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-4">
                    <div id="inventory-section" class="grid grid-cols-1 gap-4">
                        <div id="asset-code-container" class="hidden">
                            <label class="block font-bold text-gray-700 mb-1 text-xs uppercase">Cód. Patrimonial</label>
                            <input type="text" name="asset_code" value="{{ old('asset_code') }}"
                                   class="w-full border p-2 rounded focus:ring-2 focus:ring-green-500">
                        </div>
                        <div id="quantity-container" class="hidden">
                            <label class="block font-bold text-gray-700 mb-1 text-xs uppercase">Quantidade Total *</label>
                            <input type="number" name="quantity" id="quantity_field" value="{{ old('quantity', 1) }}" min="0"
                                   class="w-full border p-2 rounded focus:ring-2 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 mb-1 text-xs uppercase">Status Operacional</label>
                            <select name="status_id" class="w-full border p-2 rounded bg-white focus:ring-2 focus:ring-green-500">
                                <option value="">Selecione...</option>
                                @foreach(\App\Models\InclusiveRadar\ResourceStatus::where('is_active', true)->where('for_assistive_technology', true)->get() as $status)
                                    <option value="{{ $status->id }}" {{ old('status_id') == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- OPÇÕES FINAIS --}}
            <div class="flex flex-col md:flex-row gap-4 p-4 bg-gray-100 rounded border border-gray-300">
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="requires_training" id="requires_training" value="1" {{ old('requires_training') ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded">
                    <label for="requires_training" class="cursor-pointer text-sm font-semibold text-gray-700">Requer Treinamento Especializado</label>
                </div>
                <div class="flex items-center gap-2 ml-auto">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} class="w-5 h-5 text-green-600 rounded">
                    <label for="is_active" class="cursor-pointer text-sm font-bold text-green-700">Ativar Registro Imediatamente</label>
                </div>
            </div>

            <div class="flex gap-4 mt-4 border-t pt-6">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-12 py-3 rounded shadow-lg transition font-bold text-lg">
                    <i class="fas fa-check-circle mr-2"></i> Concluir Cadastro
                </button>
                <a href="{{ route('inclusive-radar.assistive-technologies.index') }}" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-6 py-3 rounded transition flex items-center font-bold">
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
    const oldAttributes = @json(old('attributes', []));

    function handleDigitalType() {
        const selectedOption = typeSelect.options[typeSelect.selectedIndex];
        if (!selectedOption || selectedOption.value === "") {
            [assetContainer, quantityContainer, conservationContainer].forEach(el => el.classList.add('hidden'));
            return;
        }

        const isDigital = selectedOption.getAttribute('data-digital') === '1';

        // Patrimônio e Quantidade só aparecem se NÃO for digital
        assetContainer.classList.toggle('hidden', isDigital);
        quantityContainer.classList.toggle('hidden', isDigital);

        // Conservação sempre aparece no cadastro para gerar a primeira inspeção
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
        container.innerHTML = '<p class="text-sm text-gray-500 italic px-2"><i class="fas fa-spinner fa-spin mr-2"></i>Carregando especificações...</p>';
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
                            input.name = `attributes[${attr.id}]`;
                            input.value = '1';
                            input.className = "w-5 h-5 text-green-600 rounded border-gray-300";
                            if (savedValue == '1' || savedValue === 'on') input.checked = true;
                            div.appendChild(input);
                            div.appendChild(label);
                        } else {
                            input = document.createElement('input');
                            input.type = (attr.field_type === 'integer' || attr.field_type === 'decimal') ? 'number' : (attr.field_type === 'date' ? 'date' : 'text');
                            input.name = `attributes[${attr.id}]`;
                            input.value = savedValue;
                            input.className = 'w-full border p-2 rounded focus:ring-2 focus:ring-green-500 text-sm bg-white';
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
                console.error('Erro ao carregar atributos:', err);
                container.innerHTML = '<p class="text-red-500 text-xs">Erro ao carregar atributos específicos.</p>';
            });
    }

    typeSelect.addEventListener('change', function() {
        handleDigitalType();
        loadAttributes(this.value, {});
    });

    // Inicialização ao carregar (para casos de erro de validação/old input)
    if (typeSelect.value) {
        handleDigitalType();
        loadAttributes(typeSelect.value, oldAttributes);
    }
</script>
</body>
</html>
