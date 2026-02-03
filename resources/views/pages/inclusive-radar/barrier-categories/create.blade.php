<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Categoria de Barreira</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-6 border-b pb-4 text-gray-800">Nova Categoria de Barreira</h1>

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded">
            <ul class="list-disc ml-5 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('inclusive-radar.barrier-categories.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 gap-6">
            {{-- Nome --}}
            <div>
                <label class="block font-medium text-gray-700 mb-1">Nome da Categoria</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="w-full border p-2 rounded @error('name') border-red-500 @enderror"
                       placeholder="Ex: Arquitetônica, Atitudinal, Comunicacional...">
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Descrição --}}
            <div>
                <label class="block font-medium text-gray-700 mb-1">Descrição</label>
                <textarea name="description" rows="4" class="w-full border p-2 rounded"
                          placeholder="Descreva o que este tipo de barreira engloba...">{{ old('description') }}</textarea>
            </div>

            {{-- Status --}}
            <div class="p-4 bg-gray-50 rounded border border-gray-200">
                <div class="flex items-center gap-2">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                           {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                           class="w-5 h-5 text-blue-600 rounded">
                    <label for="is_active" class="cursor-pointer font-semibold text-gray-700">Categoria Ativa para novos registros</label>
                </div>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded shadow transition font-bold">
                    Salvar Categoria
                </button>
                <a href="{{ route('inclusive-radar.barrier-categories.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded transition flex items-center">
                    Cancelar
                </a>
            </div>
        </div>
    </form>
</div>
</body>
</html>
