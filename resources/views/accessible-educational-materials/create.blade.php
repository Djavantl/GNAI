<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Material Pedagógico Acessível</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-8">
<div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-md">

    <h1 class="text-2xl font-bold mb-6 text-gray-800 border-b pb-4">
        Cadastrar Material Pedagógico Acessível
    </h1>

    {{-- Exibição de Erros --}}
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded">
            <p class="font-bold mb-1">Por favor, corrija os erros abaixo:</p>
            <ul class="list-disc ml-5 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('accessible-educational-materials.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Título --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                <input type="text" name="title" value="{{ old('title') }}"
                       class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-green-500 outline-none" required>
            </div>

            {{-- Tipo / Categoria --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo / Categoria</label>
                <input type="text" name="type" value="{{ old('type') }}"
                       class="w-full border border-gray-300 p-2 rounded focus:ring-green-500 outline-none">
            </div>

            {{-- Formato --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Formato</label>
                <input type="text" name="format" value="{{ old('format') }}"
                       class="w-full border border-gray-300 p-2 rounded focus:ring-green-500 outline-none">
            </div>

            {{-- Idioma --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Idioma</label>
                <input type="text" name="language" placeholder="Ex: pt-br" value="{{ old('language') }}"
                       class="w-full border border-gray-300 p-2 rounded focus:ring-green-500 outline-none">
            </div>

            {{-- ISBN --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ISBN</label>
                <input type="text" name="isbn" value="{{ old('isbn') }}"
                       class="w-full border border-gray-300 p-2 rounded focus:ring-green-500 outline-none">
            </div>

            {{-- Editora --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Editora</label>
                <input type="text" name="publisher" value="{{ old('publisher') }}"
                       class="w-full border border-gray-300 p-2 rounded focus:ring-green-500 outline-none">
            </div>

            {{-- Edição --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Edição</label>
                <input type="text" name="edition" value="{{ old('edition') }}"
                       class="w-full border border-gray-300 p-2 rounded focus:ring-green-500 outline-none">
            </div>

            {{-- Data de publicação --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Data de Publicação</label>
                <input type="date" name="publication_date" value="{{ old('publication_date') }}"
                       class="w-full border border-gray-300 p-2 rounded focus:ring-green-500 outline-none">
            </div>

            {{-- Número de Páginas --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Número de Páginas</label>
                <input type="number" name="pages" value="{{ old('pages') }}"
                       class="w-full border border-gray-300 p-2 rounded focus:ring-green-500 outline-none">
            </div>

            {{-- Código do Patrimônio --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Código do Patrimônio</label>
                <input type="text" name="asset_code" value="{{ old('asset_code') }}"
                       class="w-full border border-gray-300 p-2 rounded focus:ring-green-500 outline-none">
            </div>

            {{-- Localização --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Localização</label>
                <input type="text" name="location" value="{{ old('location') }}"
                       class="w-full border border-gray-300 p-2 rounded focus:ring-green-500 outline-none">
            </div>

            {{-- Estado de conservação --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado de Conservação</label>
                <input type="text" name="conservation_state" value="{{ old('conservation_state') }}"
                       class="w-full border border-gray-300 p-2 rounded focus:ring-green-500 outline-none">
            </div>

            {{-- Custo --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Custo (R$)</label>
                <input type="number" step="0.01" name="cost" value="{{ old('cost') }}"
                       class="w-full border border-gray-300 p-2 rounded focus:ring-green-500 outline-none">
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="accessible_educational_material_status_id"
                        class="w-full border border-gray-300 p-2 rounded focus:ring-green-500 outline-none">
                    <option value="">Selecione o Status</option>
                    @foreach(App\Models\AccessibleEducationalMaterialStatus::where('is_active', true)->get() as $status)
                        <option value="{{ $status->id }}" {{ old('accessible_educational_material_status_id') == $status->id ? 'selected' : '' }}>
                            {{ $status->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Recursos de acessibilidade --}}
            <div class="md:col-span-2 bg-gray-50 p-4 rounded border border-gray-200">
                <label class="block text-sm font-bold text-gray-700 mb-3">Recursos de Acessibilidade</label>

                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach(App\Models\AccessibilityFeature::where('is_active', true)->get() as $feature)
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="accessibilities[]" value="{{ $feature->id }}" id="acc_{{ $feature->id }}"
                                   class="w-4 h-4 text-green-600 rounded border-gray-300 focus:ring-green-500"
                                {{ in_array($feature->id, old('accessibilities', [])) ? 'checked' : '' }}>
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
                    @foreach(App\Models\Deficiency::where('is_active', true)->get() as $def)
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="deficiencies[]" value="{{ $def->id }}" id="def_{{ $def->id }}"
                                   class="w-4 h-4 text-green-600 rounded border-gray-300 focus:ring-green-500"
                                {{ in_array($def->id, old('deficiencies', [])) ? 'checked' : '' }}>
                            <label for="def_{{ $def->id }}" class="text-sm text-gray-700 cursor-pointer">{{ $def->name }}</label>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Opções de Status --}}
            <div class="flex flex-col gap-3">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="hidden" name="requires_training" value="0">
                    <input type="checkbox" name="requires_training" value="1"
                           class="w-5 h-5 text-green-600 rounded border-gray-300 focus:ring-green-500"
                        {{ old('requires_training') ? 'checked' : '' }}>
                    <span class="text-sm font-medium text-gray-700">Requer treinamento</span>
                </label>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1"
                           class="w-5 h-5 text-green-600 rounded border-gray-300 focus:ring-green-500"
                        {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                    <span class="text-sm font-medium text-gray-700">Ativo</span>
                </label>
            </div>

        </div>

        <div class="flex items-center gap-4 mt-10 pt-6 border-t border-gray-100">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-10 py-2 rounded font-bold shadow-md transition">
                Salvar Material
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
        div.className = 'flex items-center gap-2 mb-2';
        div.innerHTML = `
            <input type="text" name="accessibility_features[]"
                   class="w-full border border-gray-300 p-2 rounded focus:ring-green-500 outline-none"
                   placeholder="Ex: Braille, Fonte Ampliada...">
            <button type="button" onclick="this.parentElement.remove()"
                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded transition">Remover</button>
        `;
        container.appendChild(div);
        div.querySelector('input').focus();
    }
</script>

</body>
</html>
