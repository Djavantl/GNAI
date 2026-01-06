{{-- resources/views/assistive-technologies/create.blade.php --}}
    <!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Tecnologia Assistiva</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
<div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-6">Cadastrar Tecnologia Assistiva</h1>

    <form action="{{ route('assistive-technologies.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 gap-4">
            <div>
                <label class="block">Nome</label>
                <input type="text"
                       name="name"
                       value="{{ old('name') }}"
                       class="w-full border p-2 rounded">
                @error('name')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block">Descrição</label>
                <textarea name="description" class="w-full border p-2 rounded">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block">Tipo</label>
                <input type="text"
                       name="type"
                       value="{{ old('type') }}"
                       class="w-full border p-2 rounded">
            </div>

            <div>
                <label class="block mb-1">Deficiências</label>
                <select name="deficiencies[]" multiple class="w-full border p-2 rounded">
                    @foreach(App\Models\Deficiency::where('is_active', true)->get() as $def)
                        <option value="{{ $def->id }}"
                            {{ in_array($def->id, old('deficiencies', [])) ? 'selected' : '' }}>
                            {{ $def->name }}
                        </option>
                    @endforeach
                </select>
                @error('deficiencies')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block">Quantidade</label>
                <input type="number"
                       name="quantity"
                       value="{{ old('quantity', 0) }}"
                       class="w-full border p-2 rounded">
            </div>

            <div>
                <label class="block">Código do Patrimônio</label>
                <input type="text"
                       name="asset_code"
                       value="{{ old('asset_code') }}"
                       class="w-full border p-2 rounded">
            </div>

            <div>
                <label class="block">Estado de Conservação</label>
                <input type="text"
                       name="conservation_state"
                       value="{{ old('conservation_state') }}"
                       class="w-full border p-2 rounded">
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox"
                       name="requires_training"
                       value="1"
                    {{ old('requires_training') ? 'checked' : '' }}>
                <label>Requer Treinamento</label>
            </div>

            <div>
                <label class="block">Notas</label>
                <textarea name="notes" class="w-full border p-2 rounded">{{ old('notes') }}</textarea>
            </div>

            <div>
                <label class="block">Status</label>
                <select name="assistive_technology_status_id" class="w-full border p-2 rounded">
                    <option value="">Selecione um status</option>
                    @foreach(App\Models\AssistiveTechnologyStatus::where('is_active', true)->get() as $status)
                        <option value="{{ $status->id }}" {{ old('assistive_technology_status_id') == $status->id ? 'selected' : '' }}>
                            {{ $status->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" checked>
                <label>Ativo</label>
            </div>

            <div class="flex gap-4 mt-4">
                <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded">Salvar</button>
                <a href="{{ route('assistive-technologies.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded">Cancelar</a>
            </div>
        </div>
    </form>
</div>
</body>
</html>
