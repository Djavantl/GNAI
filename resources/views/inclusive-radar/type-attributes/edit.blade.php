<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Atributo</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h1 class="text-2xl font-bold text-gray-800">Editar Atributo</h1>
        <span class="text-sm text-gray-500 font-mono bg-gray-100 px-2 py-1 rounded">ID: #{{ $attribute->id }}</span>
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

    <form action="{{ route('inclusive-radar.type-attributes.update', $attribute->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block font-medium text-gray-700">Rótulo de Exibição (Label)</label>
                <input type="text" name="label" value="{{ old('label', $attribute->label) }}"
                       class="w-full border p-2 rounded @error('label') border-red-500 @enderror">
            </div>

            <div>
                <label class="block font-medium text-gray-700">Nome Técnico (Slug)</label>
                <input type="text" name="name" value="{{ old('name', $attribute->name) }}"
                       class="w-full border p-2 rounded font-mono text-sm bg-gray-50" readonly>
                <p class="text-[10px] text-gray-500 mt-1 italic">* O nome técnico não pode ser alterado após a criação.</p>
            </div>

            <div>
                <label class="block font-medium text-gray-700">Tipo de Dado</label>
                <select name="field_type" class="w-full border p-2 rounded">
                    @foreach(['string','integer','decimal','boolean','date','text'] as $type)
                        <option value="{{ $type }}" {{ old('field_type', $attribute->field_type) == $type ? 'selected' : '' }}>
                            {{ ucfirst($type) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2 flex flex-col gap-3 p-4 bg-gray-50 rounded border border-gray-200 mt-2">
                <div class="flex items-center gap-3">
                    <input type="hidden" name="is_required" value="0">
                    <input type="checkbox" name="is_required" id="is_required" value="1"
                           {{ old('is_required', $attribute->is_required) ? 'checked' : '' }} class="w-5 h-5 text-blue-600">
                    <label for="is_required" class="cursor-pointer font-medium text-gray-700">Este campo é obrigatório?</label>
                </div>

                <div class="flex items-center gap-3">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                           {{ old('is_active', $attribute->is_active) ? 'checked' : '' }} class="w-5 h-5 text-green-600">
                    <label for="is_active" class="cursor-pointer font-semibold text-green-700">Atributo Ativo</label>
                </div>
            </div>

            <div class="md:col-span-2 flex gap-4 pt-6 border-t mt-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-2 rounded shadow transition font-bold text-lg">
                    Atualizar Registro
                </button>
                <a href="{{ route('inclusive-radar.type-attributes.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded transition flex items-center">
                    Cancelar
                </a>
            </div>
        </div>
    </form>
</div>
</body>
</html>
