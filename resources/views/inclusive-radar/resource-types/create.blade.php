<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Tipo de Recurso</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-6 border-b pb-4 text-gray-800">Cadastrar Tipo de Recurso</h1>

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded">
            <ul class="list-disc ml-5 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('inclusive-radar.resource-types.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 gap-6">
            {{-- Nome --}}
            <div>
                <label class="block font-medium text-gray-700 mb-1">Nome do Tipo</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="w-full border p-2 rounded @error('name') border-red-500 @enderror"
                       placeholder="Ex: Teclados, Softwares de Leitura, Próteses...">
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Aplicação --}}
            <div class="bg-gray-50 p-4 rounded border border-gray-200">
                <label class="block font-bold text-gray-700 mb-3 text-sm uppercase tracking-wide">Este tipo se aplica a:</label>
                <div class="flex flex-col gap-3">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="for_assistive_technology" value="1" {{ old('for_assistive_technology') ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded">
                        <span class="text-gray-700 font-medium">Tecnologias Assistivas (Hardware/Equipamentos)</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="for_educational_material" value="1" {{ old('for_educational_material') ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded">
                        <span class="text-gray-700 font-medium">Materiais Didáticos / Educacionais</span>
                    </label>
                </div>
            </div>

            {{-- Ativo --}}
            <div class="flex items-center gap-2 p-3 bg-green-50 rounded border border-green-100">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} class="w-4 h-4 text-green-600">
                <label for="is_active" class="cursor-pointer font-semibold text-green-700">Tipo de Recurso Ativo</label>
            </div>

            <div class="flex gap-4 pt-4 border-t">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-10 py-3 rounded shadow-lg transition font-bold text-lg">
                    Salvar Tipo
                </button>
                <a href="{{ route('inclusive-radar.resource-types.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded transition flex items-center">
                    Cancelar
                </a>
            </div>
        </div>
    </form>
</div>
</body>
</html>
