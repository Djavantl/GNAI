<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tipo de Recurso - GNAI</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow border-t-4 border-blue-600">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h1 class="text-2xl font-bold text-gray-800">Editar Tipo de Recurso</h1>
        {{-- CORREÇÃO: Alterado de $type para $resourceType --}}
        <span class="text-sm text-gray-500 font-mono bg-gray-100 px-2 py-1 rounded">ID: #{{ $resourceType->id }}</span>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded">
            <p class="font-bold mb-1">Atenção:</p>
            <ul class="list-disc ml-5 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- CORREÇÃO: URL da rota usando $resourceType->id --}}
    <form action="{{ route('inclusive-radar.resource-types.update', $resourceType->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-6">
            {{-- Nome --}}
            <div>
                <label class="block font-medium text-gray-700 mb-1 font-bold">Nome do Tipo</label>
                {{-- CORREÇÃO: value usando $resourceType->name --}}
                <input type="text" name="name" value="{{ old('name', $resourceType->name) }}"
                       class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Aplicação --}}
            <div class="bg-gray-50 p-4 rounded border border-gray-200">
                <label class="block font-bold text-gray-700 mb-3 text-sm uppercase tracking-wide">Aplicação:</label>
                <div class="flex flex-col gap-3">
                    <label class="flex items-center gap-3 cursor-pointer">
                        {{-- Hidden inputs garantem que o valor '0' seja enviado se desmarcado --}}
                        <input type="hidden" name="for_assistive_technology" value="0">
                        <input type="checkbox" name="for_assistive_technology" value="1"
                               {{ old('for_assistive_technology', $resourceType->for_assistive_technology) ? 'checked' : '' }}
                               class="w-5 h-5 text-blue-600 rounded">
                        <span class="text-gray-700 font-medium">Tecnologias Assistivas</span>
                    </label>

                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="hidden" name="for_educational_material" value="0">
                        <input type="checkbox" name="for_educational_material" value="1"
                               {{ old('for_educational_material', $resourceType->for_educational_material) ? 'checked' : '' }}
                               class="w-5 h-5 text-blue-600 rounded">
                        <span class="text-gray-700 font-medium">Materiais Didáticos</span>
                    </label>
                </div>
            </div>

            {{-- Ativo --}}
            <div class="flex items-center gap-2 p-3 bg-gray-50 rounded border border-gray-200">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                       {{ old('is_active', $resourceType->is_active) ? 'checked' : '' }}
                       class="w-4 h-4 text-green-600 rounded">
                <label for="is_active" class="cursor-pointer font-bold text-green-700 uppercase text-sm">Cadastro Ativo</label>
            </div>

            <div class="flex gap-4 pt-4 border-t">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-2 rounded shadow transition font-bold text-lg">
                    <i class="fas fa-save mr-2"></i> Atualizar Registro
                </button>
                <a href="{{ route('inclusive-radar.resource-types.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded transition flex items-center font-semibold">
                    Cancelar
                </a>
            </div>
        </div>
    </form>
</div>
</body>
</html>
