<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurar Atributos - GNAI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-8">
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow border-t-4 border-blue-600">
    <h1 class="text-2xl font-bold mb-2 text-gray-800">Configurar Atributos por Tipo</h1>
    <p class="text-gray-600 mb-6 border-b pb-4">Defina quais campos estarão disponíveis para cada tipo de recurso.</p>

    {{-- Bloco de Erros --}}
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded">
            <ul class="list-disc ml-5 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('inclusive-radar.type-attribute-assignments.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 gap-6">

            {{-- Seleção do Tipo de Recurso --}}
            <div>
                <label class="block font-bold text-gray-700 mb-2 italic">1. Para qual Tipo de Recurso deseja configurar atributos?</label>
                <select name="type_id" class="w-full border p-3 rounded focus:ring-2 focus:ring-blue-500 @error('type_id') border-red-500 @enderror bg-gray-50">
                    <option value="">-- Selecione um tipo --</option>
                    @foreach($types as $type)
                        <option value="{{ $type->id }}" {{ old('type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }} ({{ $type->for_assistive_technology ? 'Assistiva' : 'Educacional' }})
                        </option>
                    @endforeach
                </select>
                @error('type_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Seleção dos Atributos (Checkboxes) --}}
            <div>
                <label class="block font-bold text-gray-700 mb-3 italic">2. Quais atributos (campos) este Tipo deve possuir?</label>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 bg-gray-50 p-5 rounded-lg border border-gray-200">
                    @foreach($attributes as $attribute)
                        <label class="flex items-start gap-3 p-3 bg-white border rounded hover:border-blue-400 hover:shadow-sm transition cursor-pointer group">
                            <div class="flex items-center h-5">
                                <input type="checkbox"
                                       name="attribute_ids[]"
                                       value="{{ $attribute->id }}"
                                       {{ is_array(old('attribute_ids')) && in_array($attribute->id, old('attribute_ids')) ? 'checked' : '' }}
                                       class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500 cursor-pointer">
                            </div>

                            <div class="flex flex-col leading-tight">
                                <span class="text-sm font-bold text-gray-700 group-hover:text-blue-700">
                                    {{ $attribute->label }}
                                </span>
                                <span class="text-[10px] uppercase font-mono text-gray-400 mt-1">
                                    Tipo: {{ $attribute->field_type }}
                                </span>
                            </div>
                        </label>
                    @endforeach
                </div>

                @error('attribute_ids')
                <p class="text-red-500 text-xs mt-2 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <div class="bg-blue-50 p-4 rounded border border-blue-100 flex items-start gap-3">
                <i class="fas fa-info-circle text-blue-600 mt-1"></i>
                <p class="text-sm text-blue-800">
                    <strong>Dica:</strong> Se o tipo selecionado já possuir atributos vinculados, os novos marcados aqui serão adicionados à lista existente. Para remover vínculos, utilize a opção <strong>"Gerenciar"</strong> na tela de listagem.
                </p>
            </div>

            <hr class="my-4">

            {{-- Botões --}}
            <div class="flex gap-4">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-10 py-3 rounded shadow-lg transition font-bold text-lg">
                    Salvar Configuração
                </button>
                <a href="{{ route('inclusive-radar.type-attribute-assignments.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded transition flex items-center font-semibold">
                    Cancelar
                </a>
            </div>
        </div>
    </form>
</div>
</body>
</html>
