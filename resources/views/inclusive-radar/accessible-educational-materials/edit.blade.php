<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Material Pedagógico Acessível</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-8">
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h1 class="text-2xl font-bold text-gray-800">Editar Material Pedagógico</h1>
        <span
            class="text-sm text-gray-500 font-mono bg-gray-100 px-2 py-1 rounded">ID: #{{ $accessibleEducationalMaterial->id }}</span>
    </div>

    {{-- Exibição de Erros --}}
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded">
            <p class="font-bold mb-1">Por favor, corrija os seguintes erros:</p>
            <ul class="list-disc ml-5 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('inclusive-radar.accessible-educational-materials.update', $accessibleEducationalMaterial) }}" method="POST"
          enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-4">

            {{-- Título --}}
            <div>
                <label class="block font-medium">Título do Material</label>
                <input type="text" name="title"
                       value="{{ old('title', $accessibleEducationalMaterial->title) }}"
                       class="w-full border p-2 rounded @error('title') border-red-500 @enderror">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium">Tipo / Categoria</label>
                    <input type="text" name="type" value="{{ old('type', $accessibleEducationalMaterial->type) }}"
                           class="w-full border p-2 rounded">
                </div>
                <div>
                    <label class="block font-medium">Formato</label>
                    <input type="text" name="format" value="{{ old('format', $accessibleEducationalMaterial->format) }}"
                           class="w-full border p-2 rounded">
                </div>
            </div>

            {{-- SEÇÃO DE IMAGENS ATUAIS (Estilo TA) --}}
            @if($accessibleEducationalMaterial->images->count() > 0)
                <div class="mt-2">
                    <label class="block font-bold mb-2 text-gray-700">Imagens Atuais
                        ({{ $accessibleEducationalMaterial->images->count() }})</label>
                    <div class="grid grid-cols-3 sm:grid-cols-5 gap-4 bg-gray-50 p-4 rounded border border-gray-200">
                        @foreach($accessibleEducationalMaterial->images as $image)
                            <div class="relative group border rounded p-1 bg-white shadow-sm">
                                <img src="{{ asset('storage/' . $image->path) }}" alt="Imagem"
                                     class="h-24 w-full object-contain rounded">

                                <button type="button"
                                        onclick="if(confirm('Deseja excluir esta imagem?')) document.getElementById('delete-image-{{ $image->id }}').submit();"
                                        class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full p-1 shadow hover:bg-red-700 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ADICIONAR NOVAS IMAGENS (Igual ao Create) --}}
            <div class="bg-blue-50 p-4 rounded border border-blue-100">
                <label class="block font-semibold text-blue-800 mb-1">Adicionar Novas Imagens</label>
                <input type="file" name="images[]" multiple accept="image/*"
                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                <p class="text-xs text-blue-600 mt-1 italic">As novas fotos serão adicionadas à galeria atual.</p>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block font-medium">Idioma</label>
                    <input type="text" name="language"
                           value="{{ old('language', $accessibleEducationalMaterial->language) }}"
                           class="w-full border p-2 rounded">
                </div>
                <div>
                    <label class="block font-medium">ISBN</label>
                    <input type="text" name="isbn" value="{{ old('isbn', $accessibleEducationalMaterial->isbn) }}"
                           class="w-full border p-2 rounded">
                </div>
                <div>
                    <label class="block font-medium">Nº de Páginas</label>
                    <input type="number" name="pages" value="{{ old('pages', $accessibleEducationalMaterial->pages) }}"
                           class="w-full border p-2 rounded">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium">Editora</label>
                    <input type="text" name="publisher"
                           value="{{ old('publisher', $accessibleEducationalMaterial->publisher) }}"
                           class="w-full border p-2 rounded">
                </div>
                <div>
                    <label class="block font-medium">Edição</label>
                    <input type="text" name="edition"
                           value="{{ old('edition', $accessibleEducationalMaterial->edition) }}"
                           class="w-full border p-2 rounded">
                </div>
            </div>

            {{-- Recursos de Acessibilidade --}}
            <div class="bg-gray-50 p-4 rounded border border-gray-200">
                <label class="block font-bold text-gray-700 mb-2">Recursos de Acessibilidade</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                    @foreach(\App\Models\InclusiveRadar\AccessibilityFeature::where('is_active', true)->get() as $feature)
                        <div class="flex items-center gap-2">
                            {{-- Note que usei 'accessibilities' que é o nome da relação no seu Model --}}
                            <input type="checkbox" name="accessibility_features[]" value="{{ $feature->id }}"
                                   id="acc_{{ $feature->id }}"
                                {{ in_array($feature->id, old('accessibility_features', $accessibleEducationalMaterial->accessibilities->pluck('id')->toArray())) ? 'checked' : '' }}>
                            <label for="acc_{{ $feature->id }}"
                                   class="text-sm cursor-pointer">{{ $feature->name }}</label>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Deficiências --}}
            <div>
                <label class="block font-medium mb-1">Público-alvo (Deficiências)</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2 bg-gray-50 p-4 rounded border border-gray-200">
                    @foreach(\App\Models\SpecializedEducationalSupport\Deficiency::where('is_active', true)->get() as $def)
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="deficiencies[]" value="{{ $def->id }}" id="def_{{ $def->id }}"
                                {{ in_array($def->id, old('deficiencies', $accessibleEducationalMaterial->deficiencies->pluck('id')->toArray())) ? 'checked' : '' }}>
                            <label for="def_{{ $def->id }}" class="text-sm cursor-pointer">{{ $def->name }}</label>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block font-medium">Patrimônio</label>
                    <input type="text" name="asset_code"
                           value="{{ old('asset_code', $accessibleEducationalMaterial->asset_code) }}"
                           class="w-full border p-2 rounded">
                </div>
                <div>
                    <label class="block font-medium">Custo (R$)</label>
                    <input type="number" step="0.01" name="cost"
                           value="{{ old('cost', $accessibleEducationalMaterial->cost) }}"
                           class="w-full border p-2 rounded">
                </div>
                <div>
                    <label class="block font-medium">Status</label>
                    <select name="accessible_educational_material_status_id" class="w-full border p-2 rounded">
                        <option value="">Selecione</option>
                        @foreach(\App\Models\InclusiveRadar\AccessibleEducationalMaterialStatus::where('is_active', true)->get() as $status)
                            <option value="{{ $status->id }}"
                                {{ old('accessible_educational_material_status_id', $accessibleEducationalMaterial->accessible_educational_material_status_id) == $status->id ? 'selected' : '' }}>
                                {{ $status->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex flex-col gap-2 p-2 bg-gray-50 rounded">
                <div class="flex items-center gap-2">
                    <input type="hidden" name="requires_training" value="0">
                    <input type="checkbox" name="requires_training" id="requires_training" value="1"
                        {{ old('requires_training', $accessibleEducationalMaterial->requires_training) ? 'checked' : '' }}>
                    <label for="requires_training" class="cursor-pointer">Requer Treinamento</label>
                </div>
                <div class="flex items-center gap-2">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                        {{ old('is_active', $accessibleEducationalMaterial->is_active) ? 'checked' : '' }}>
                    <label for="is_active" class="cursor-pointer font-semibold text-green-700">Cadastro Ativo</label>
                </div>
            </div>

            <hr class="my-4">

            <div class="flex gap-4">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-2 rounded shadow transition font-bold text-lg">
                    Atualizar Material
                </button>
                <a href="{{ route('inclusive-radar.accessible-educational-materials.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded transition flex items-center">
                    Cancelar
                </a>
            </div>
        </div>
    </form>

    {{-- FORMULÁRIOS INVISÍVEIS PARA DELETAR IMAGENS --}}
    @foreach($accessibleEducationalMaterial->images as $image)
        <form id="delete-image-{{ $image->id }}"
              action="{{ route('inclusive-radar.accessible-educational-materials.images.destroy', $image->id) }}"
              method="POST"
              class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endforeach
</div>
</body>
</html>
