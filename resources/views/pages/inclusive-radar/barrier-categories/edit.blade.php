<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Categoria</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h1 class="text-2xl font-bold text-gray-800">Editar Categoria</h1>
        <span class="text-xs text-gray-400 font-mono">ID: #{{ $barrierCategory->id }}</span>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded">
            <ul class="list-disc ml-5 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('inclusive-radar.barrier-categories.update', $barrierCategory->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-6">
            <div>
                <label class="block font-medium text-gray-700 mb-1">Nome da Categoria</label>
                <input type="text" name="name"
                       value="{{ old('name', $barrierCategory->name) }}"
                       class="w-full border p-2 rounded @error('name') border-red-500 @enderror">
            </div>

            <div>
                <label class="block font-medium text-gray-700 mb-1">Descrição</label>
                <textarea name="description" rows="4"
                          class="w-full border p-2 rounded">{{ old('description', $barrierCategory->description) }}</textarea>
            </div>

            <div class="p-4 bg-gray-50 rounded border border-gray-200">
                <div class="flex items-center gap-2">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                           {{ old('is_active', $barrierCategory->is_active) ? 'checked' : '' }}
                           class="w-5 h-5 text-blue-600 rounded">
                    <label for="is_active" class="cursor-pointer font-semibold text-gray-700">Categoria Ativa</label>
                </div>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded shadow transition font-bold">
                    Atualizar Categoria
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
