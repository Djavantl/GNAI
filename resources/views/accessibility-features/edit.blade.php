<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Recurso de Acessibilidade</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-6">Editar Recurso de Acessibilidade</h1>

    @if($errors->any())
        <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded">
            <ul class="list-disc ml-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('accessibility-features.update', $accessibilityFeature) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-gray-700 mb-1">Nome</label>
            <input type="text" name="name" value="{{ old('name', $accessibilityFeature->name) }}"
                   class="w-full border border-gray-300 p-2 rounded focus:ring-blue-500 outline-none" required>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 mb-1">Descrição</label>
            <textarea name="description" rows="3"
                      class="w-full border border-gray-300 p-2 rounded focus:ring-blue-500 outline-none">{{ old('description', $accessibilityFeature->description) }}</textarea>
        </div>

        <div class="mb-6 flex items-center gap-3">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $accessibilityFeature->is_active) ? 'checked' : '' }}
            class="w-5 h-5 text-green-600 rounded border-gray-300 focus:ring-green-500">
            <span class="text-gray-700 font-medium">Ativo</span>
        </div>

        <div class="flex gap-4">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded font-bold">
                Atualizar
            </button>
            <a href="{{ route('accessibility-features.index') }}"
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded font-medium">
                Cancelar
            </a>
        </div>
    </form>
</div>
</body>
</html>
