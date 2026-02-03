<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Atributos - GNAI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-8">
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-2 text-gray-800">Gerenciar Atributos do Tipo</h1>
    <p class="text-gray-600 mb-6 italic">Configurando campos para: <strong>{{ $type->name }}</strong></p>

    <form action="{{ route('inclusive-radar.type-attribute-assignments.update', $type->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-6">

            {{-- O tipo fica como um campo escondido ou um select desabilitado para contexto --}}
            <input type="hidden" name="type_id" value="{{ $type->id }}">

            <div>
                <label class="block font-bold text-gray-700 mb-3">Marque os atributos que este recurso deve possuir:</label>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 bg-gray-50 p-5 rounded-lg border border-gray-200">
                    @foreach($attributes as $attribute)
                        <label class="flex items-start gap-3 p-3 bg-white border rounded hover:border-blue-400 hover:shadow-sm transition cursor-pointer group">
                            <div class="flex items-center h-5">
                                <input type="checkbox"
                                       name="attribute_ids[]"
                                       value="{{ $attribute->id }}"
                                       {{ in_array($attribute->id, $assignedAttributeIds) ? 'checked' : '' }}
                                       class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500 cursor-pointer">
                            </div>

                            <div class="flex flex-col leading-tight">
                                <span class="text-sm font-bold text-gray-700 group-hover:text-blue-700">{{ $attribute->label }}</span>
                                <span class="text-[10px] uppercase font-mono text-gray-400 mt-1">{{ $attribute->field_type }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="bg-blue-50 p-4 rounded border border-blue-100 flex items-start gap-3">
                <i class="fas fa-info-circle text-blue-600 mt-1"></i>
                <p class="text-sm text-blue-800 italic">
                    Ao desmarcar um atributo, ele deixará de aparecer no formulário de cadastro de recursos deste tipo.
                </p>
            </div>

            <hr class="my-4">

            <div class="flex gap-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-10 py-3 rounded shadow-lg transition font-bold text-lg">
                    Salvar Alterações
                </button>
                <a href="{{ route('inclusive-radar.type-attribute-assignments.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded transition flex items-center">
                    Cancelar
                </a>
            </div>
        </div>
    </form>
</div>
</body>
</html>
