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
    {{-- Cabeçalho --}}
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
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded shadow-sm text-sm">
            <p class="font-bold italic">Atenção: Existem erros no preenchimento.</p>
            <ul class="list-disc ml-5">
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('inclusive-radar.assistive-technologies.update', $assistiveTechnology->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-6">
            {{-- Dados Básicos --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block font-bold text-gray-700 mb-1">Nome da Tecnologia *</label>
                    <input type="text" name="name" value="{{ old('name', $assistiveTechnology->name) }}" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block font-bold text-gray-700 mb-1">Descrição Detalhada</label>
                    <textarea name="description" rows="2" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500">{{ old('description', $assistiveTechnology->description) }}</textarea>
                </div>
            </div>

            <hr>

            {{-- HISTÓRICO DE INSPEÇÕES --}}
            <div class="mt-2">
                <label class="block font-bold mb-4 text-gray-800 uppercase text-sm tracking-wide">
                    <i class="fas fa-history mr-2 text-blue-600"></i>Histórico de Vistorias e Fotos
                </label>

                <div class="space-y-4 max-h-[500px] overflow-y-auto pr-2">
                    @forelse($assistiveTechnology->inspections()->with('images')->latest('inspection_date')->get() as $inspection)
                        <div class="border rounded-lg bg-gray-50 overflow-hidden shadow-sm">
                            <div class="bg-gray-100 px-4 py-2 border-b flex justify-between items-center">
                                <span class="text-xs font-bold text-blue-700">
                                    <i class="far fa-calendar-alt mr-1"></i>
                                    {{ $inspection->inspection_date->format('d/m/Y') }}
                                </span>
                                <span class="text-[10px] font-bold uppercase px-2 py-1 rounded bg-white border border-gray-300">
                                    {{ $inspection->type->label() }}
                                </span>
                            </div>
                            <div class="p-4">
                                <div class="mb-3 flex justify-between items-start">
                                    <div>
                                        <p class="text-[10px] font-bold text-gray-500 uppercase">Estado registrado:</p>
                                        <p class="text-sm font-semibold text-gray-800">{{ $inspection->state->label() }}</p>
                                    </div>
                                    @if($inspection->description)
                                        <div class="max-w-[60%] text-right">
                                            <p class="text-[10px] text-gray-600 italic">"{{ $inspection->description }}"</p>
                                        </div>
                                    @endif
                                </div>

                                @if($inspection->images->count() > 0)
                                    <div class="grid grid-cols-4 sm:grid-cols-6 gap-2">
                                        @foreach($inspection->images as $img)
                                            <a href="{{ asset('storage/' . $img->path) }}" target="_blank" class="group relative block border rounded p-1 bg-white hover:border-blue-500 transition">
                                                <img src="{{ asset('storage/' . $img->path) }}" class="h-16 w-full object-cover rounded">
                                                <div class="absolute inset-0 bg-blue-600 opacity-0 group-hover:opacity-10 rounded flex items-center justify-center"></div>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 border-2 border-dashed rounded-lg text-gray-400">
                            <p class="text-sm">Nenhuma vistoria registrada anteriormente.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- NOVA VISTORIA (Sincronizada com o Service) --}}
            <div class="bg-blue-50 p-5 rounded-lg border-2 border-blue-200 mt-4">
                <h3 class="text-blue-800 font-bold mb-4 flex items-center gap-2 uppercase text-xs">
                    <i class="fas fa-plus-circle"></i> Atualizar Estado / Adicionar Fotos
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1 text-[10px] uppercase">Estado de Conservação *</label>
                        <select name="conservation_state" class="w-full border p-2 rounded text-sm bg-white focus:ring-2 focus:ring-blue-500">
                            @foreach(\App\Enums\InclusiveRadar\ConservationState::cases() as $state)
                                <option value="{{ $state->value }}" {{ old('conservation_state', $assistiveTechnology->conservation_state->value) == $state->value ? 'selected' : '' }}>
                                    {{ $state->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1 text-[10px] uppercase">Tipo da Inspeção</label>
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
                        <label class="block font-bold text-gray-700 mb-1 text-[10px] uppercase">Data da Vistoria</label>
                        <input type="date" name="inspection_date" value="{{ date('Y-m-d') }}" class="w-full border p-2 rounded text-sm">
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block font-bold text-gray-700 mb-1 text-[10px] uppercase">Notas da nova atualização</label>
                    <textarea name="inspection_description" rows="2" class="w-full border p-2 rounded text-sm focus:ring-2 focus:ring-blue-500" placeholder="Descreva mudanças físicas ou manutenções realizadas..."></textarea>
                </div>

                <div class="mt-4">
                    <label class="block font-bold text-blue-800 mb-1 text-[10px] uppercase font-semibold">Anexar Novas Fotos</label>
                    <input type="file" name="images[]" multiple accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-xs file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                    <p class="text-[9px] text-gray-400 mt-1 italic">As fotos antigas serão mantidas no histórico acima.</p>
                </div>
            </div>

            {{-- Tipo e Atributos Dinâmicos --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 mb-1 text-sm uppercase">Tipo / Categoria *</label>
                    <select name="type_id" id="type_id" class="w-full border p-2 rounded bg-white">
                        @foreach(\App\Models\InclusiveRadar\ResourceType::where('for_assistive_technology', true)->where('is_active', true)->get() as $type)
                            <option value="{{ $type->id }}" data-digital="{{ $type->is_digital ? '1' : '0' }}" {{ old('type_id', $assistiveTechnology->type_id) == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div id="dynamic-attributes-container" class="hidden">
                <label class="block font-bold text-blue-900 mb-2 border-l-4 border-blue-500 pl-2 text-xs uppercase">Especificações Técnicas</label>
                <div id="dynamic-attributes" class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg border"></div>
            </div>

            {{-- Público e Inventário --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block font-bold text-gray-700 mb-2 text-xs uppercase">Público-alvo *</label>
                    <div class="grid grid-cols-2 gap-2 bg-gray-50 p-3 rounded border h-full">
                        @foreach($deficiencies as $def)
                            <div class="flex items-center gap-2">
                                <input type="checkbox" name="deficiencies[]" value="{{ $def->id }}" id="def_{{ $def->id }}" {{ in_array($def->id, old('deficiencies', $assistiveTechnology->deficiencies->pluck('id')->toArray())) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded">
                                <label for="def_{{ $def->id }}" class="text-xs cursor-pointer">{{ $def->name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-4">
                    @php $activeLoans = $assistiveTechnology->loans()->whereIn('status', ['active', 'late'])->count(); @endphp
                    <div id="asset-code-container">
                        <label class="block font-bold text-gray-700 mb-1 text-[10px] uppercase">Cód. Patrimonial</label>
                        <input type="text" name="asset_code" value="{{ old('asset_code', $assistiveTechnology->asset_code) }}" class="w-full border p-2 rounded text-sm">
                    </div>
                    <div id="quantity-container">
                        <label class="block font-bold text-gray-700 mb-1 text-[10px] uppercase">Quantidade Total *</label>
                        <input type="number" name="quantity" id="quantity_field" value="{{ old('quantity', $assistiveTechnology->quantity) }}" min="{{ $activeLoans }}" class="w-full border p-2 rounded text-sm">
                        @if($activeLoans > 0)
                            <p class="text-[9px] text-amber-600 font-bold mt-1 uppercase"><i class="fas fa-lock mr-1"></i> {{ $activeLoans }} em uso/empréstimo</p>
                        @endif
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1 text-[10px] uppercase">Status Operacional</label>
                        <select name="status_id" class="w-full border p-2 rounded bg-white text-sm">
                            @foreach(\App\Models\InclusiveRadar\ResourceStatus::where('is_active', true)->where('for_assistive_technology', true)->get() as $status)
                                <option value="{{ $status->id }}" {{ old('status_id', $assistiveTechnology->status_id) == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Opções Finais --}}
            <div class="flex flex-col md:flex-row gap-4 p-4 bg-gray-100 rounded border border-gray-300">
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="requires_training" id="requires_training" value="1" {{ old('requires_training', $assistiveTechnology->requires_training) ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded">
                    <label for="requires_training" class="cursor-pointer text-sm font-semibold text-gray-700">Requer Treinamento</label>
                </div>
                <div class="flex items-center gap-2 ml-auto">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $assistiveTechnology->is_active) ? 'checked' : '' }} class="w-5 h-5 text-green-600 rounded">
                    <label for="is_active" class="cursor-pointer text-sm font-bold text-green-700 uppercase">Cadastro Ativo</label>
                </div>
            </div>

            <div class="flex gap-4 mt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-10 py-3 rounded shadow-lg transition font-bold text-lg">
                    <i class="fas fa-save mr-2"></i> Atualizar Registro
                </button>
                <a href="{{ route('inclusive-radar.assistive-technologies.index') }}" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-6 py-3 rounded transition flex items-center font-bold">
                    Voltar
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
    const quantityField = document.getElementById('quantity_field');

    function handleDigitalType() {
        const isDigital = typeSelect.options[typeSelect.selectedIndex]?.getAttribute('data-digital') === '1';
        assetContainer.classList.toggle('hidden', isDigital);
        quantityContainer.classList.toggle('hidden', isDigital);
        if (isDigital) quantityField.value = '';
    }

    function loadAttributes(typeId, currentValues = {}) {
        if (!typeId) return;
        container.innerHTML = '<p class="text-xs italic px-2">Carregando especificações...</p>';
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
                        let input;
                        if (attr.field_type === 'boolean') {
                            div.className = "flex items-center gap-3 p-2 bg-white rounded border border-gray-100 shadow-sm";
                            div.innerHTML = `<input type="hidden" name="attributes[${attr.id}]" value="0"><input type="checkbox" name="attributes[${attr.id}]" value="1" class="w-4 h-4 text-blue-600" ${val == '1' ? 'checked' : ''}><label class="text-xs font-bold text-gray-600">${attr.label}</label>`;
                        } else {
                            div.innerHTML = `<label class="text-xs font-bold text-gray-600 uppercase">${attr.label}</label><input type="${['integer','decimal'].includes(attr.field_type)?'number':(attr.field_type==='date'?'date':'text')}" name="attributes[${attr.id}]" value="${val}" class="w-full border p-2 rounded text-xs bg-white focus:ring-2 focus:ring-blue-500">`;
                        }
                        container.appendChild(div);
                    });
                } else outerContainer.classList.add('hidden');
            });
    }

    // Inicialização
    const initialValues = @json($attributeValues ?? []);
    handleDigitalType();
    loadAttributes(typeSelect.value, initialValues);

    typeSelect.addEventListener('change', () => {
        handleDigitalType();
        loadAttributes(typeSelect.value, {});
    });
</script>
</body>
</html>
