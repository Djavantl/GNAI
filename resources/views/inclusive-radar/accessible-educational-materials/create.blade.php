<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Material Pedagógico Acessível</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-6 border-b pb-4">Cadastrar Material Pedagógico Acessível</h1>

    {{-- IMPORTANTE: enctype="multipart/form-data" incluído para suportar as imagens --}}
    <form action="{{ route('inclusive-radar.accessible-educational-materials.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 gap-4">

            {{-- Título --}}
            <div>
                <label class="block font-medium">Título do Material</label>
                <input type="text" name="title" value="{{ old('title') }}"
                       class="w-full border p-2 rounded @error('title') border-red-500 @enderror">
                @error('title')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium">Tipo / Categoria</label>
                    <input type="text" name="type" value="{{ old('type') }}" class="w-full border p-2 rounded">
                </div>
                <div>
                    <label class="block font-medium">Formato</label>
                    <input type="text" name="format" value="{{ old('format') }}" class="w-full border p-2 rounded">
                </div>
            </div>

            {{-- SEÇÃO DE IMAGENS: Estilo TA --}}
            <div class="bg-blue-50 p-4 rounded border border-blue-100">
                <label class="block font-semibold text-blue-800 mb-1">Imagens do Material</label>
                <input type="file"
                       name="images[]"
                       multiple
                       accept="image/*"
                       class="w-full text-sm text-gray-500
                              file:mr-4 file:py-2 file:px-4
                              file:rounded-md file:border-0
                              file:text-sm file:font-semibold
                              file:bg-blue-600 file:text-white
                              hover:file:bg-blue-700 cursor-pointer">
                <p class="text-xs text-gray-500 mt-1 italic">Você pode selecionar várias fotos de uma vez (JPEG, PNG,
                    WEBP).</p>

                @error('images')
                <span class="text-red-500 text-sm block">{{ $message }}</span>
                @enderror

                @if($errors->has('images.*'))
                    @foreach($errors->get('images.*') as $messages)
                        @foreach($messages as $message)
                            <span class="text-red-500 text-sm block">{{ $message }}</span>
                        @endforeach
                    @endforeach
                @endif
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block font-medium">Idioma</label>
                    <input type="text" name="language" value="{{ old('language') }}" class="w-full border p-2 rounded"
                           placeholder="Ex: pt-br">
                </div>
                <div>
                    <label class="block font-medium">ISBN</label>
                    <input type="text" name="isbn" value="{{ old('isbn') }}" class="w-full border p-2 rounded">
                    @error('isbn') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block font-medium">Nº de Páginas</label>
                    <input type="number" name="pages" value="{{ old('pages') }}" class="w-full border p-2 rounded">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium">Editora</label>
                    <input type="text" name="publisher" value="{{ old('publisher') }}"
                           class="w-full border p-2 rounded">
                </div>
                <div>
                    <label class="block font-medium">Edição</label>
                    <input type="text" name="edition" value="{{ old('edition') }}" class="w-full border p-2 rounded">
                </div>
            </div>

            {{-- Recursos de Acessibilidade --}}
            <div class="bg-gray-50 p-4 rounded border border-gray-200">
                <label class="block font-bold text-gray-700 mb-2">Recursos de Acessibilidade</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                    @foreach(\App\Models\InclusiveRadar\AccessibilityFeature::where('is_active', true)->get() as $feature)
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="accessibility_features[]" value="{{ $feature->id }}"
                                   id="acc_{{ $feature->id }}"
                                {{ in_array($feature->id, old('accessibility_features', [])) ? 'checked' : '' }}>
                            <label for="acc_{{ $feature->id }}"
                                   class="text-sm cursor-pointer">{{ $feature->name }}</label>
                        </div>
                    @endforeach
                </div>
                @error('accessibility_features') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Deficiências --}}
            <div>
                <label class="block font-medium mb-1">Público-alvo (Deficiências)</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2 bg-gray-50 p-4 rounded border border-gray-200">
                    @foreach(\App\Models\SpecializedEducationalSupport\Deficiency::where('is_active', true)->get() as $def)
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="deficiencies[]" value="{{ $def->id }}" id="def_{{ $def->id }}"
                                {{ in_array($def->id, old('deficiencies', [])) ? 'checked' : '' }}>
                            <label for="def_{{ $def->id }}" class="text-sm cursor-pointer">{{ $def->name }}</label>
                        </div>
                    @endforeach
                </div>
                @error('deficiencies') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block font-medium">Patrimônio</label>
                    <input type="text" name="asset_code" value="{{ old('asset_code') }}"
                           class="w-full border p-2 rounded">
                    @error('asset_code') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block font-medium">Custo (R$)</label>
                    <input type="number" step="0.01" name="cost" value="{{ old('cost') }}"
                           class="w-full border p-2 rounded">
                </div>
                <div>
                    <label class="block font-medium">Status</label>
                    <select name="accessible_educational_material_status_id" class="w-full border p-2 rounded">
                        <option value="">Selecione</option>
                        @foreach(\App\Models\InclusiveRadar\AccessibleEducationalMaterialStatus::where('is_active', true)->get() as $status)
                            <option
                                value="{{ $status->id }}" {{ old('accessible_educational_material_status_id') == $status->id ? 'selected' : '' }}>
                                {{ $status->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex flex-col gap-2 p-2 bg-gray-50 rounded">
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="requires_training" id="requires_training"
                           value="1" {{ old('requires_training') ? 'checked' : '' }}>
                    <label for="requires_training" class="cursor-pointer">Requer Treinamento</label>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="is_active"
                           value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                    <label for="is_active" class="cursor-pointer font-semibold text-green-700">Cadastro Ativo</label>
                </div>
            </div>

            <hr class="my-4">

            <div class="flex gap-4">
                <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-8 py-2 rounded shadow transition font-bold text-lg">
                    Salvar Material
                </button>
                <a href="{{ route('inclusive-radar.accessible-educational-materials.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded transition flex items-center">
                    Cancelar
                </a>
            </div>
        </div>
    </form>
</div>
</body>
</html>
