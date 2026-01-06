<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Tecnologia Assistiva</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
<div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-6">Editar Tecnologia Assistiva</h1>

    <form action="{{ route('assistive-technologies.update', $assistiveTechnology->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-4">
            {{-- Nome --}}
            <div>
                <label class="block font-semibold">Nome</label>
                <input type="text"
                       name="name"
                       value="{{ old('name', $assistiveTechnology->name) }}"
                       class="w-full border p-2 rounded @error('name') border-red-500 @enderror">
                @error('name')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            {{-- Descrição --}}
            <div>
                <label class="block font-semibold">Descrição</label>
                <textarea name="description" class="w-full border p-2 rounded">{{ old('description', $assistiveTechnology->description) }}</textarea>
            </div>

            {{-- Tipo --}}
            <div>
                <label class="block font-semibold">Tipo</label>
                <input type="text"
                       name="type"
                       value="{{ old('type', $assistiveTechnology->type) }}"
                       class="w-full border p-2 rounded">
            </div>

            {{-- Deficiências (Usando a variável $deficiencies injetada pelo Controller) --}}
            <div>
                <label class="block mb-1 font-semibold">Deficiências</label>
                <select name="deficiencies[]" multiple class="w-full border p-2 rounded h-32">
                    @foreach($deficiencies as $def)
                        <option value="{{ $def->id }}"
                            {{ in_array($def->id, old('deficiencies', $assistiveTechnology->deficiencies->pluck('id')->toArray())) ? 'selected' : '' }}>
                            {{ $def->name }}
                        </option>
                    @endforeach
                </select>
                <small class="text-gray-500">Pressione Ctrl/Command para selecionar múltiplos</small>
                @error('deficiencies')
                <br><span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            {{-- Quantidade e Código do Patrimônio --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold">Quantidade</label>
                    <input type="number"
                           name="quantity"
                           value="{{ old('quantity', $assistiveTechnology->quantity) }}"
                           class="w-full border p-2 rounded">
                </div>
                <div>
                    <label class="block font-semibold">Código do Patrimônio</label>
                    <input type="text"
                           name="asset_code"
                           value="{{ old('asset_code', $assistiveTechnology->asset_code) }}"
                           class="w-full border p-2 rounded @error('asset_code') border-red-500 @enderror">
                    @error('asset_code')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Estado de Conservação --}}
            <div>
                <label class="block font-semibold">Estado de Conservação</label>
                <input type="text"
                       name="conservation_state"
                       value="{{ old('conservation_state', $assistiveTechnology->conservation_state) }}"
                       class="w-full border p-2 rounded">
            </div>

            {{-- Requer Treinamento --}}
            <div class="flex items-center gap-2">
                {{-- Input hidden garante que o valor '0' seja enviado se o checkbox estiver desmarcado --}}
                <input type="hidden" name="requires_training" value="0">
                <input type="checkbox"
                       name="requires_training"
                       id="requires_training"
                       value="1"
                    {{ old('requires_training', $assistiveTechnology->requires_training) ? 'checked' : '' }}>
                <label for="requires_training">Requer Treinamento</label>
            </div>

            {{-- Notas --}}
            <div>
                <label class="block font-semibold">Notas</label>
                <textarea name="notes" class="w-full border p-2 rounded">{{ old('notes', $assistiveTechnology->notes) }}</textarea>
            </div>

            {{-- Status --}}
            <div>
                <label class="block font-semibold">Status</label>
                <select name="assistive_technology_status_id" class="w-full border p-2 rounded">
                    <option value="">Selecione um status</option>
                    {{-- Aqui você pode injetar os status via controller ou usar a query se for algo global --}}
                    @foreach(App\Models\AssistiveTechnologyStatus::where('is_active', true)->get() as $status)
                        <option value="{{ $status->id }}"
                            {{ old('assistive_technology_status_id', $assistiveTechnology->assistive_technology_status_id) == $status->id ? 'selected' : '' }}>
                            {{ $status->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Status Ativo --}}
            <div class="flex items-center gap-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox"
                       name="is_active"
                       id="is_active"
                       value="1"
                    {{ old('is_active', $assistiveTechnology->is_active) ? 'checked' : '' }}>
                <label for="is_active">Ativo</label>
            </div>

            {{-- Botões de Ação --}}
            <div class="flex gap-4 mt-6">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
                    Salvar Alterações
                </button>
                <a href="{{ route('assistive-technologies.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600 transition">
                    Cancelar
                </a>
            </div>
        </div>
    </form>
</div>
</body>
</html>
