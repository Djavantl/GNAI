<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Novo Recurso de Acessibilidade</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-6">
        Cadastrar Recurso de Acessibilidade
    </h1>

    @if($errors->any())
        <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded">
            <ul class="list-disc ml-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('inclusive-radar.accessibility-features.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 gap-4">
            <div>
                <label class="block text-gray-700 mb-1">Nome</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="w-full border p-2 rounded focus:ring-blue-500 outline-none" required>
            </div>

            <div>
                <label class="block text-gray-700 mb-1">Descrição</label>
                <textarea name="description" rows="3"
                          class="w-full border p-2 rounded focus:ring-blue-500 outline-none">{{ old('description') }}</textarea>
            </div>

            <div class="flex items-center gap-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', 1) == 1 ? 'checked' : '' }}
                class="w-5 h-5 text-green-600 rounded border-gray-300 focus:ring-green-500">
                <span class="text-gray-700 font-medium">Ativo</span>
            </div>

            <div class="flex gap-4 mt-4">
                <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded font-bold">
                    Salvar
                </button>
                <a href="{{ route('inclusive-radar.accessibility-features.index') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded font-medium">
                    Cancelar
                </a>
            </div>
        </div>
    </form>
</div>
</body>
</html>
