<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atributos de Recursos - GNAI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-8">

<div class="max-w-6xl mx-auto">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Atributos de Recursos</h1>
            <p class="text-gray-600">Definição de campos dinâmicos (metadados) para os tipos de recursos.</p>
        </div>

        <a href="{{ route('inclusive-radar.type-attributes.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded shadow transition font-bold flex items-center gap-2">
            <i class="fas fa-plus-circle"></i>
            Novo Atributo
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-200 text-green-800 p-4 rounded mb-6 flex items-center gap-3 shadow-sm">
            <i class="fas fa-check-circle text-green-600"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white p-6 rounded shadow border-t-4 border-blue-600">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                <tr class="border-b-2 border-gray-100">
                    <th class="py-3 px-4 font-bold text-gray-700">Rótulo (Label) / Slug</th>
                    <th class="py-3 px-4 font-bold text-gray-700 text-center">Tipo de Campo</th>
                    <th class="py-3 px-4 font-bold text-gray-700 text-center">Obrigatório</th>
                    <th class="py-3 px-4 font-bold text-gray-700 text-center">Status</th>
                    <th class="py-3 px-4 font-bold text-gray-700 text-right">Ações</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                @forelse($attributes as $attr)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-4 px-4 align-middle">
                            <span class="font-bold text-gray-900 block">{{ $attr->label }}</span>
                            <span class="text-xs text-gray-400 font-mono italic">{{ $attr->name }}</span>
                        </td>

                        <td class="py-4 px-4 text-center align-middle">
                            <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs font-mono border">
                                {{ strtoupper($attr->field_type) }}
                            </span>
                        </td>

                        <td class="py-4 px-4 text-center align-middle">
                            @if($attr->is_required)
                                <span class="text-orange-600 font-bold text-xs bg-orange-50 px-2 py-1 rounded border border-orange-100">SIM</span>
                            @else
                                <span class="text-gray-400 text-xs">Não</span>
                            @endif
                        </td>

                        <td class="py-4 px-4 text-center align-middle">
                            @if($attr->is_active)
                                <span class="text-green-600 bg-green-50 px-2 py-1 rounded text-xs font-bold border border-green-100">ATIVO</span>
                            @else
                                <span class="text-red-600 bg-red-50 px-2 py-1 rounded text-xs font-bold border border-red-100">INATIVO</span>
                            @endif
                        </td>

                        <td class="py-4 px-4 text-right align-middle">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('inclusive-radar.type-attributes.edit', $attr) }}"
                                   class="text-blue-600 hover:bg-blue-50 px-3 py-1 rounded transition border border-blue-100 text-sm font-semibold">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('inclusive-radar.type-attributes.toggle', $attr) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-3 py-1 rounded transition border text-sm font-semibold {{ $attr->is_active ? 'text-amber-600 border-amber-100 hover:bg-amber-50' : 'text-green-600 border-green-100 hover:bg-green-50' }}">
                                        <i class="fas {{ $attr->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                    </button>
                                </form>

                                <form action="{{ route('inclusive-radar.type-attributes.destroy', $attr) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Excluir este atributo permanentemente?')" class="text-red-600 hover:bg-red-50 px-3 py-1 rounded transition border border-red-100 text-sm font-semibold">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-gray-400">
                            <p>Nenhum atributo cadastrado.</p>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
