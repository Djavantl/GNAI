<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Tecnologia Assistiva - GNAI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 p-4 md:p-8">
<div class="max-w-5xl mx-auto bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">

    <div class="bg-slate-800 p-6 flex justify-between items-center text-white">
        <div>
            <h1 class="text-2xl font-bold">Nova Tecnologia Assistiva</h1>
            <p class="text-slate-400 text-sm">Registro completo de recursos e vistoria de entrada</p>
        </div>
        <i class="fas fa-microchip text-4xl opacity-20"></i>
    </div>

    @if($errors->any())
        <div class="m-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700">
            <span class="font-bold flex items-center gap-2 mb-2"><i class="fas fa-exclamation-circle"></i> Erros encontrados:</span>
            <ul class="list-disc ml-5 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('inclusive-radar.assistive-technologies.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-2 space-y-8">

                <section>
                    <h3 class="text-blue-700 font-bold uppercase text-xs tracking-wider mb-4 border-b pb-1">1. Identificação do Recurso</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700">Nome da Tecnologia / Equipamento *</label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 border p-2.5">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Categoria / Tipo *</label>
                            <select name="type_id" id="type_id" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 border p-2.5 bg-white">
                                <option value="">Selecione...</option>
                                @foreach($resourceTypes as $type)
                                    <option value="{{ $type->id }}"
                                            data-digital="{{ $type->is_digital ? '1' : '0' }}"
                                        {{ old('type_id', $assistiveTechnology->type_id ?? '') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div id="asset_code_container">
                            <label class="block text-sm font-semibold text-gray-700">Patrimônio / Tombamento</label>
                            <input type="text" name="asset_code" value="{{ old('asset_code') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2.5">
                        </div>
                    </div>
                </section>

                <section class="bg-slate-50 p-5 rounded-xl border border-slate-200 shadow-sm">
                    <h3 class="text-slate-700 font-bold uppercase text-xs tracking-wider mb-4 flex items-center gap-2">
                        <i class="fas fa-clipboard-check text-blue-600"></i> 2. Detalhes da Vistoria Inicial
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Tipo de Inspeção *</label>
                            <select name="inspection_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2 bg-white">
                                @foreach(\App\Enums\InclusiveRadar\InspectionType::cases() as $type)
                                    <option value="{{ $type->value }}" {{ old('inspection_type', \App\Enums\InclusiveRadar\InspectionType::INITIAL->value) == $type->value ? 'selected' : '' }}>
                                        {{ $type->name }} </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Data da Inspeção *</label>
                            <input type="date" name="inspection_date" value="{{ old('inspection_date', date('Y-m-d')) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2">
                        </div>

                        <div id="conservation_container">
                            <label class="block text-sm font-semibold text-gray-700">Estado de Conservação *</label>
                            <select name="conservation_state" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2 bg-white">
                                @foreach(\App\Enums\InclusiveRadar\ConservationState::cases() as $state)
                                    <option value="{{ $state->value }}" {{ old('conservation_state') == $state->value ? 'selected' : '' }}>
                                        {{ $state->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Fotos de Evidência</label>
                            <input type="file" name="images[]" multiple class="mt-1 block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700">Parecer Técnico / Descrição da Vistoria</label>
                            <textarea name="inspection_description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2 text-sm"
                                      placeholder="Descreva as condições físicas e funcionais do item na entrada">{{ old('inspection_description') }}</textarea>
                        </div>
                    </div>
                </section>

                <section id="dynamic-attributes-container" class="hidden">
                    <h3 class="text-blue-700 font-bold uppercase text-xs tracking-wider mb-4 border-b pb-1">3. Especificações Técnicas</h3>
                    <div id="dynamic-attributes" class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white p-4 rounded-lg border">
                    </div>
                </section>
            </div>

            <div class="space-y-6">
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                    <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-boxes text-blue-500"></i> Gestão de Estoque
                    </h3>

                    <div id="quantity_container" class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Quantidade Total *</label>
                        <input type="number" name="quantity" id="quantity_input" value="{{ old('quantity', 1) }}" min="0"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status do Recurso</label>
                        <select name="status_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2 bg-white">
                            <option value="">Selecione...</option>
                            @foreach(\App\Models\InclusiveRadar\ResourceStatus::where('is_active', true)->get() as $status)
                                <option value="{{ $status->id }}" {{ old('status_id') == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                    <h3 class="text-sm font-bold text-gray-800 mb-4">Público-alvo *</h3>
                    <div class="space-y-2 max-h-60 overflow-y-auto pr-2">
                        @foreach(\App\Models\SpecializedEducationalSupport\Deficiency::where('is_active', true)->get() as $def)
                            <label class="flex items-center p-2 rounded hover:bg-slate-50 cursor-pointer border border-transparent hover:border-slate-200 transition-all">
                                <input type="checkbox" name="deficiencies[]" value="{{ $def->id }}"
                                       {{ in_array($def->id, old('deficiencies', [])) ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                <span class="ml-3 text-sm text-gray-600 font-medium">{{ $def->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-3">
                    <label class="flex items-center gap-3 p-4 bg-blue-50/50 border border-blue-100 rounded-lg cursor-pointer">
                        <input type="checkbox" name="requires_training" value="1" {{ old('requires_training') ? 'checked' : '' }} class="w-5 h-5 text-blue-600 border-gray-300 rounded">
                        <div>
                            <span class="block text-sm font-bold text-blue-900">Requer Treinamento</span>
                            <span class="text-xs text-blue-700">Indica necessidade de capacitação para uso</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-4 bg-green-50/50 border border-green-100 rounded-lg cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} class="w-5 h-5 text-green-600 border-gray-300 rounded">
                        <div>
                            <span class="block text-sm font-bold text-green-900">Ativar no Sistema</span>
                            <span class="text-xs text-green-700">Fica visível para empréstimos imediatamente</span>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <div class="mt-10 pt-6 border-t flex flex-col-reverse md:flex-row justify-end gap-3">
            <a href="{{ route('inclusive-radar.assistive-technologies.index') }}"
               class="px-8 py-3 rounded-lg text-gray-600 hover:bg-gray-100 font-bold transition-all text-center">
                Voltar para Listagem
            </a>
            <button type="submit" class="px-12 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold shadow-lg shadow-blue-200 transition-all transform hover:-translate-y-1">
                <i class="fas fa-save mr-2"></i> Finalizar Cadastro
            </button>
        </div>
    </form>
</div>

<script>
    // Mesma lógica JS para atributos e comportamento digital/físico
    const typeSelect = document.getElementById('type_id');
    const dynamicAttrContainer = document.getElementById('dynamic-attributes-container');
    const dynamicAttrList = document.getElementById('dynamic-attributes');
    const assetContainer = document.getElementById('asset_code_container');
    const qtyContainer = document.getElementById('quantity_container');
    const qtyInput = document.getElementById('quantity_input');

    function handleTypeChange() {
        const selected = typeSelect.options[typeSelect.selectedIndex];
        const isDigital = selected?.dataset.digital === '1';

        // Esconde mas não remove os campos (para manter acessibilidade)
        assetContainer.style.opacity = isDigital ? '0.5' : '1';
        qtyContainer.style.opacity = isDigital ? '0.5' : '1';

        if(isDigital) {
            qtyInput.value = '';
            qtyInput.disabled = true;
        } else {
            qtyInput.disabled = false;
            if(!qtyInput.value) qtyInput.value = 1;
        }
    }

    function loadAttributes(typeId) {
        if (!typeId) {
            dynamicAttrContainer.classList.add('hidden');
            return;
        }

        dynamicAttrList.innerHTML = '<div class="col-span-2 text-sm text-gray-400 italic">Buscando campos específicos...</div>';
        dynamicAttrContainer.classList.remove('hidden');

        fetch(`/inclusive-radar/resource-types/${typeId}/attributes`)
            .then(res => res.json())
            .then(data => {
                dynamicAttrList.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(attr => {
                        const div = document.createElement('div');
                        div.className = "space-y-1";
                        div.innerHTML = `
                            <label class="block text-xs font-bold text-gray-500 uppercase">${attr.label} ${attr.is_required ? '*' : ''}</label>
                            <input type="${attr.field_type === 'integer' ? 'number' : 'text'}"
                                   name="attributes[${attr.id}]"
                                   ${attr.is_required ? 'required' : ''}
                                   class="block w-full border border-gray-300 rounded-md p-2 text-sm focus:ring-blue-500">
                        `;
                        dynamicAttrList.appendChild(div);
                    });
                } else {
                    dynamicAttrContainer.classList.add('hidden');
                }
            });
    }

    typeSelect.addEventListener('change', (e) => {
        handleTypeChange();
        loadAttributes(e.target.value);
    });

    if(typeSelect.value) {
        handleTypeChange();
        loadAttributes(typeSelect.value);
    }
</script>
</body>
</html>
