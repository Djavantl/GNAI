<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Atributo</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-6 border-b pb-4 text-gray-800">Novo Atributo Personalizado</h1>

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded">
            <ul class="list-disc ml-5 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('inclusive-radar.type-attributes.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Label --}}
            <div class="md:col-span-2">
                <label class="block font-medium text-gray-700">Rótulo de Exibição (Label)</label>
                <input type="text" name="label" value="{{ old('label') }}"
                       class="w-full border p-2 rounded @error('label') border-red-500 @enderror"
                       placeholder="Ex: Versão do Software, Cor do Chassis, Material...">
            </div>

            {{-- Nome Técnico --}}
            <div>
                <label class="block font-medium text-gray-700">Nome Técnico (Slug / DB Name)</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="w-full border p-2 rounded font-mono text-sm"
                       placeholder="Ex: versao_software (sem espaços)">
            </div>

            {{-- Field Type --}}
            <div>
                <label class="block font-medium text-gray-700">Tipo de Dado</label>
                <select name="field_type" class="w-full border p-2 rounded">
                    <option value="string" {{ old('field_type') == 'string' ? 'selected' : '' }}>Texto Curto (String)</option>
                    <option value="text" {{ old('field_type') == 'text' ? 'selected' : '' }}>Texto Longo (TextArea)</option>
                    <option value="integer" {{ old('field_type') == 'integer' ? 'selected' : '' }}>Número Inteiro</option>
                    <option value="decimal" {{ old('field_type') == 'decimal' ? 'selected' : '' }}>Número Decimal</option>
                    <option value="boolean" {{ old('field_type') == 'boolean' ? 'selected' : '' }}>Sim/Não (Booleano)</option>
                    <option value="date" {{ old('field_type') == 'date' ? 'selected' : '' }}>Data</option>
                </select>
            </div>

            {{-- Configurações --}}
            <div class="md:col-span-2 flex flex-col gap-3 p-4 bg-gray-50 rounded border border-gray-200 mt-2">
                <div class="flex items-center gap-3">
                    <input type="hidden" name="is_required" value="0">
                    <input type="checkbox" name="is_required" id="is_required" value="1" {{ old('is_required') ? 'checked' : '' }} class="w-5 h-5 text-blue-600">
                    <label for="is_required" class="cursor-pointer font-medium text-gray-700">Este campo é obrigatório no preenchimento?</label>
                </div>

                <div class="flex items-center gap-3">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} class="w-5 h-5 text-green-600">
                    <label for="is_active" class="cursor-pointer font-semibold text-green-700">Atributo Ativo para Uso</label>
                </div>
            </div>

            <div class="md:col-span-2 flex gap-4 pt-6 border-t">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-10 py-3 rounded shadow-lg transition font-bold text-lg">
                    Salvar Atributo
                </button>
                <a href="{{ route('inclusive-radar.type-attributes.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded transition flex items-center">
                    Cancelar
                </a>
            </div>
        </div>
    </form>
</div>
</body>
</html>
