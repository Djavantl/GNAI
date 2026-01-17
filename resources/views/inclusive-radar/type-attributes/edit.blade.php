<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Atributo - GNAI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-8">
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow border-t-4 border-blue-600">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h1 class="text-2xl font-bold text-gray-800">Editar Atributo</h1>
        {{-- CORREÇÃO: Alterado de $attribute para $typeAttribute --}}
        <span class="text-sm text-gray-500 font-mono bg-gray-100 px-2 py-1 rounded">ID: #{{ $typeAttribute->id }}</span>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded">
            <p class="font-bold mb-1 italic">Verifique os erros abaixo:</p>
            <ul class="list-disc ml-5 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- CORREÇÃO: Action da rota e ID alterados para $typeAttribute --}}
    <form action="{{ route('inclusive-radar.type-attributes.update', $typeAttribute->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Rótulo --}}
            <div class="md:col-span-2">
                <label class="block font-bold text-gray-700 mb-1">Rótulo de Exibição (Label)</label>
                <input type="text" name="label" value="{{ old('label', $typeAttribute->label) }}"
                       class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500 @error('label') border-red-500 @enderror">
                @error('label') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Slug (Somente leitura) --}}
            <div>
                <label class="block font-bold text-gray-700 mb-1">Nome Técnico (Slug)</label>
                <input type="text" name="name" value="{{ old('name', $typeAttribute->name) }}"
                       class="w-full border p-2 rounded font-mono text-sm bg-gray-100 cursor-not-allowed" readonly>
                <p class="text-[10px] text-gray-500 mt-1 italic">* O nome técnico não pode ser alterado após a criação.</p>
            </div>

            {{-- Tipo de Dado --}}
            <div>
                <label class="block font-bold text-gray-700 mb-1">Tipo de Dado</label>
                <select name="field_type" class="w-full border p-2 rounded bg-white focus:ring-2 focus:ring-blue-500">
                    @foreach(['string' => 'Texto Curto', 'integer' => 'Número Inteiro', 'decimal' => 'Decimal', 'boolean' => 'Sim/Não (Checkbox)', 'date' => 'Data', 'text' => 'Texto Longo'] as $value => $label)
                        <option value="{{ $value }}" {{ old('field_type', $typeAttribute->field_type) == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Checkboxes de Configuração --}}
            <div class="md:col-span-2 flex flex-col gap-3 p-4 bg-gray-50 rounded border border-gray-200 mt-2">
                <div class="flex items-center gap-3 group">
                    <input type="hidden" name="is_required" value="0">
                    <input type="checkbox" name="is_required" id="is_required" value="1"
                           {{ old('is_required', $typeAttribute->is_required) ? 'checked' : '' }}
                           class="w-5 h-5 text-blue-600 rounded border-gray-300">
                    <label for="is_required" class="cursor-pointer font-medium text-gray-700">Este campo é obrigatório?</label>
                </div>

                <div class="flex items-center gap-3 group">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                           {{ old('is_active', $typeAttribute->is_active) ? 'checked' : '' }}
                           class="w-5 h-5 text-green-600 rounded border-gray-300">
                    <label for="is_active" class="cursor-pointer font-bold text-green-700">Atributo Ativo</label>
                </div>
            </div>

            {{-- Botões --}}
            <div class="md:col-span-2 flex gap-4 pt-6 border-t mt-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-2 rounded shadow-lg transition font-bold text-lg flex-1 md:flex-none">
                    <i class="fas fa-save mr-2"></i> Atualizar Registro
                </button>
                <a href="{{ route('inclusive-radar.type-attributes.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded transition flex items-center font-semibold">
                    Cancelar
                </a>
            </div>
        </div>
    </form>
</div>
</body>
</html>
