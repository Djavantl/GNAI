<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Tecnologia Assistiva</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-6 border-b pb-4 text-gray-800">Cadastrar Tecnologia Assistiva</h1>

    {{-- Bloco de Erros de Validação --}}
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

    <form action="{{ route('assistive-technologies.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 gap-4">

            {{-- Nome do Equipamento --}}
            <div>
                <label class="block font-medium text-gray-700">Nome da Tecnologia / Equipamento</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="w-full border p-2 rounded @error('name') border-red-500 @enderror"
                       placeholder="Ex: Teclado Adaptado, Mouse de Esfera...">
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Descrição --}}
            <div>
                <label class="block font-medium text-gray-700">Descrição Detalhada</label>
                <textarea name="description" rows="3" class="w-full border p-2 rounded"
                          placeholder="Descreva as características técnicas e funcionalidades...">{{ old('description') }}</textarea>
            </div>

            {{-- SEÇÃO DE IMAGENS (Padronizada com MPA) --}}
            <div class="bg-blue-50 p-4 rounded border border-blue-100">
                <label class="block font-semibold text-blue-800 mb-1">Imagens do Equipamento</label>
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
                <p class="text-xs text-blue-600 mt-1 italic">Dica: Você pode selecionar várias fotos de uma vez.</p>

                @error('images') <span class="text-red-500 text-sm block">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium text-gray-700">Tipo / Categoria</label>
                    <input type="text" name="type" value="{{ old('type') }}"
                           class="w-full border p-2 rounded" placeholder="Ex: Periférico, Software...">
                </div>
                <div>
                    <label class="block font-medium text-gray-700">Quantidade em Estoque</label>
                    <input type="number" name="quantity" value="{{ old('quantity', 0) }}" class="w-full border p-2 rounded">
                </div>
            </div>

            {{-- Público-alvo (Deficiências) - Agora com Checkboxes igual ao MPA --}}
            <div>
                <label class="block font-bold text-gray-700 mb-2">Público-alvo (Deficiências)</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2 bg-gray-50 p-4 rounded border border-gray-200">
                    @foreach(App\Models\Deficiency::where('is_active', true)->get() as $def)
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="deficiencies[]" value="{{ $def->id }}" id="def_{{ $def->id }}"
                                {{ (is_array(old('deficiencies')) && in_array($def->id, old('deficiencies'))) ? 'checked' : '' }}>
                            <label for="def_{{ $def->id }}" class="text-sm cursor-pointer hover:text-blue-600 transition">{{ $def->name }}</label>
                        </div>
                    @endforeach
                </div>
                @error('deficiencies') <span class="text-red-500 text-sm block mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block font-medium text-gray-700">Código Patrimonial</label>
                    <input type="text" name="asset_code" value="{{ old('asset_code') }}" class="w-full border p-2 rounded">
                    @error('asset_code') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block font-medium text-gray-700">Estado de Conservação</label>
                    <input type="text" name="conservation_state" value="{{ old('conservation_state') }}"
                           class="w-full border p-2 rounded" placeholder="Ex: Novo, Ótimo, Regular...">
                </div>
                <div>
                    <label class="block font-medium text-gray-700">Status Operacional</label>
                    <select name="assistive_technology_status_id" class="w-full border p-2 rounded">
                        <option value="">Selecione um status</option>
                        @foreach(App\Models\AssistiveTechnologyStatus::where('is_active', true)->get() as $status)
                            <option value="{{ $status->id }}" {{ old('assistive_technology_status_id') == $status->id ? 'selected' : '' }}>
                                {{ $status->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Notas Internas --}}
            <div>
                <label class="block font-medium text-gray-700">Observações / Notas Internas</label>
                <textarea name="notes" rows="2" class="w-full border p-2 rounded" placeholder="Informações de uso interno...">{{ old('notes') }}</textarea>
            </div>

            {{-- Configurações de Ativo e Treinamento --}}
            <div class="flex flex-col gap-2 p-3 bg-gray-100 rounded border border-gray-300">
                <div class="flex items-center gap-2">
                    <input type="hidden" name="requires_training" value="0">
                    <input type="checkbox" name="requires_training" id="requires_training" value="1" {{ old('requires_training') ? 'checked' : '' }}
                    class="w-4 h-4 text-blue-600">
                    <label for="requires_training" class="cursor-pointer text-gray-700">Requer Treinamento para o uso</label>
                </div>
                <div class="flex items-center gap-2">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                    class="w-4 h-4 text-green-600">
                    <label for="is_active" class="cursor-pointer font-semibold text-green-700">Equipamento com Cadastro Ativo</label>
                </div>
            </div>

            <hr class="my-4">

            {{-- Botões de Ação --}}
            <div class="flex gap-4">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-10 py-3 rounded shadow-lg transition font-bold text-lg">
                    Salvar Tecnologia
                </button>
                <a href="{{ route('assistive-technologies.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded transition flex items-center">
                    Cancelar
                </a>
            </div>
        </div>
    </form>
</div>
</body>
</html>
