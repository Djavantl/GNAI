<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Material Pedagógico Acessível</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-8">
<div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-md">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Editar Material Pedagógico</h1>
        <span class="text-sm text-gray-500">ID: #{{ $accessibleEducationalMaterial->id }}</span>
    </div>

    {{-- Exibição de Erros --}}
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded">
            <p class="font-bold mb-1">Por favor, corrija os seguintes erros:</p>
            <ul class="list-disc ml-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('accessible-educational-materials.update', $accessibleEducationalMaterial) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Título --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                <input type="text" name="title"
                       value="{{ old('title', $accessibleEducationalMaterial->title) }}"
                       class="w-full border border-gray-300 p-2 rounded focus:ring-blue-500 focus:border-blue-500">
            </div>

            {{-- Tipo / Categoria --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo / Categoria</label>
                <input type="text" name="type"
                       value="{{ old('type', $accessibleEducationalMaterial->type) }}"
                       class="w-full border border-gray-300 p-2 rounded focus:ring-blue-500">
            </div>

            {{-- Formato --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Formato</label>
                <input type="text" name="format"
                       value="{{ old('format', $accessibleEducationalMaterial->format) }}"
                       class="w-full border border-gray-300 p-2 rounded focus:ring-blue-500">
            </div>

            {{-- Idioma --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Idioma</label>
                <input type="text" name="language" placeholder="Ex: pt-br"
                       value="{{ old('language', $accessibleEducationalMaterial->language) }}"
                       class="w-full border border-gray-300 p-2 rounded focus:ring-blue-500">
            </div>

            {{-- ISBN --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ISBN</label>
                <input type="text" name="isbn"
                       value="{{ old('isbn', $accessibleEducationalMaterial->isbn) }}"
                       class="w-full border border-gray-300 p-2 rounded focus:ring-blue-500">
            </div>

            {{-- Editora --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Editora</label>
                <input type="text" name="publisher"
                       value="{{ old('publisher', $accessibleEducationalMaterial->publisher) }}"
                       class="w-full border border-gray-300 p-2 rounded focus:ring-blue-500">
            </div>

            {{-- Edição --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Edição</label>
                <input type="text" name="edition"
                       value="{{ old('edition', $accessibleEducationalMaterial->edition) }}"
                       class="w-full border border-gray-300 p-2 rounded focus:ring-blue-500">
            </div>

            {{-- Data de Publicação --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Data de Publicação</label>
                <input type="date" name="publication_date"
                       value="{{ old('publication_date', optional($accessibleEducationalMaterial->publication_date)->format('Y-m-d')) }}"
                       class="w-full border border-gray-300 p-2 rounded focus:ring-blue-500">
            </div>

            {{-- Número de Páginas --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Número de Páginas</label>
                <input type="number" name="pages"
                       value="{{ old('pages', $accessibleEducationalMaterial->pages) }}"
                       class="w-full border border-gray-300 p-2 rounded focus:ring-blue-500">
            </div>

            {{-- Código do Patrimônio --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Código do Patrimônio</label>
                <input type="text" name="asset_code"
                       value="{{ old('asset_code', $accessibleEducationalMaterial->asset_code) }}"
                       class="w-full border border-gray-300 p-2 rounded focus:ring-blue-500">
            </div>

            {{-- Localização --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Localização</label>
                <input type="text" name="location"
                       value="{{ old('location', $accessibleEducationalMaterial->location) }}"
                       class="w-full border border-gray-300 p-2 rounded focus:ring-blue-500">
            </div>

            {{-- Estado de Conservação --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado de Conservação</label>
                <input type="text" name="conservation_state"
                       value="{{ old('conservation_state', $accessibleEducationalMaterial->conservation_state) }}"
                       class="w-full border border-gray-300 p-2 rounded focus:ring-blue-500">
            </div>

            {{-- Custo --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Custo (R$)</label>
                <input type="number" step="0.01" name="cost"
                       value="{{ old('cost', $accessibleEducationalMaterial->cost) }}"
                       class="w-full border border-gray-300 p-2 rounded focus:ring-blue-500">
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="accessible_educational_material_status_id"
                        class="w-full border border-gray-300 p-2 rounded focus:ring-blue-500">
                    <option value="">Selecione o Status</option>
                    @foreach(App\Models\AccessibleEducationalMaterialStatus::where('is_active', true)->get() as $status)
                        <option value="{{ $status->id }}"
                            {{ old('accessible_educational_material_status_id', $accessibleEducationalMaterial->accessible_educational_material_status_id) == $status->id ? 'selected' : '' }}>
                            {{ $status->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Recursos de acessibilidade --}}
            <div class="md:col-span-2 bg-gray-50 p-4 rounded border border-gray-200">
                <label class="block text-sm font-bold text-gray-700 mb-3">Recursos de Acessibilidade</label>

                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @php
                        $selectedFeatures = old('accessibilities', $accessibleEducationalMaterial->accessibilities->pluck('id')->toArray());
                    @endphp

                    @foreach(App\Models\AccessibilityFeature::where('is_active', true)->get() as $feature)
                        <div class="flex items-center gap-2">
                            <input type="checkbox"
                                   name="accessibilities[]"
                                   value="{{ $feature->id }}"
                                   id="acc_{{ $feature->id }}"
                                   class="w-4 h-4 text-green-600 rounded border-gray-300 focus:ring-green-500"
                                {{ in_array($feature->id, $selectedFeatures) ? 'checked' : '' }}>
                            <label for="acc_{{ $feature->id }}" class="text-sm text-gray-700 cursor-pointer">
                                {{ $feature->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Deficiências --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-gray-700 mb-2">Público-alvo (Deficiências)</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 bg-gray-50 p-4 rounded border border-gray-200">
                    @foreach($deficiencies as $def)
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="deficiencies[]" value="{{ $def->id }}" id="def_{{ $def->id }}"
                                   class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500"
                                {{ in_array($def->id, old('deficiencies', $accessibleEducationalMaterial->deficiencies->pluck('id')->toArray())) ? 'checked' : '' }}>
                            <label for="def_{{ $def->id }}" class="text-sm text-gray-700 cursor-pointer">{{ $def->name }}</label>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Checkboxes de Status --}}
            <div class="flex flex-col gap-3">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="hidden" name="requires_training" value="0">
                    <input type="checkbox" name="requires_training" value="1"
                           class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500"
                        {{ old('requires_training', $accessibleEducationalMaterial->requires_training) ? 'checked' : '' }}>
                    <span class="text-sm font-medium text-gray-700">Requer treinamento para uso</span>
                </label>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1"
                           class="w-5 h-5 text-green-600 rounded border-gray-300 focus:ring-blue-500"
                        {{ old('is_active', $accessibleEducationalMaterial->is_active) ? 'checked' : '' }}>
                    <span class="text-sm font-medium text-gray-700">Registro Ativo</span>
                </label>
            </div>

        </div>

        <div class="flex items-center gap-4 mt-10 pt-6 border-t border-gray-100">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-2 rounded font-bold shadow-lg transition">
                Salvar Alterações
            </button>

            <a href="{{ route('accessible-educational-materials.index') }}"
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-8 py-2 rounded font-medium transition">
                Cancelar
            </a>
        </div>

    </form>
</div>

<script>
    function addFeature() {
        const container = document.getElementById('features-container');
        const div = document.createElement('div');
        div.className = 'flex items-center gap-2 mb-2 feature-row';
        div.innerHTML = `
            <input type="text" name="accessibility_features[]"
                   class="w-full border border-gray-300 p-2 rounded focus:ring-blue-500"
                   placeholder="Ex: Audiodescrição, Braille...">
            <button type="button" onclick="this.parentElement.remove()"
                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded transition">
                Remover
            </button>
        `;
        container.appendChild(div);
        div.querySelector('input').focus();
    }
</script>

</body>
</html>
