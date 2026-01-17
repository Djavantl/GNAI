<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vínculos de Atributos - GNAI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-8">

<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Vínculos de Atributos</h1>
            <p class="text-gray-600">Gerencie quais campos cada tipo de recurso deve possuir.</p>
        </div>

        <a href="{{ route('inclusive-radar.type-attribute-assignments.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded shadow transition font-bold flex items-center gap-2">
            <i class="fas fa-plus-circle"></i>
            Novo Vínculo em Massa
        </a>
    </div>

    {{-- Mensagens de Feedback --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-200 text-green-800 p-4 rounded mb-6 shadow-sm flex items-center gap-2">
            <i class="fas fa-check-circle text-green-600"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white p-6 rounded shadow border-t-4 border-blue-600">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                <tr class="border-b-2 border-gray-100">
                    <th class="py-3 px-4 font-bold text-gray-700 w-1/4">Tipo de Recurso</th>
                    <th class="py-3 px-4 font-bold text-gray-700">Atributos Atrelados (Campos)</th>
                    <th class="py-3 px-4 font-bold text-gray-700 text-right w-1/6">Ações</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                @php
                    // Agrupamos os registros pelo nome do tipo para evitar repetição na tabela
                    $groupedAssignments = $assignments->groupBy('type.name');
                @endphp

                @forelse($groupedAssignments as $typeName => $items)
                    @php
                        $firstItem = $items->first();
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-4 px-4 align-top">
                            <span class="font-bold text-blue-800 text-lg">{{ $typeName }}</span>
                            <div class="text-[10px] text-gray-400 uppercase mt-1">
                                {{ $firstItem->type->for_assistive_technology ? 'Tecnologia' : '' }}
                                {{ $firstItem->type->for_assistive_technology && $firstItem->type->for_educational_material ? ' | ' : '' }}
                                {{ $firstItem->type->for_educational_material ? 'Material' : '' }}
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex flex-wrap gap-2">
                                @foreach($items as $item)
                                    <span class="inline-flex items-center bg-blue-50 text-blue-700 text-xs font-semibold px-2.5 py-0.5 rounded border border-blue-200">
                                        {{ $item->attribute->label }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="py-4 px-4 text-right align-top">
                            <div class="flex justify-end gap-2">
                                {{-- Botão Editar (Gerenciar todos os atributos deste tipo) --}}
                                <a href="{{ route('inclusive-radar.type-attribute-assignments.edit', $firstItem->type_id) }}"
                                   class="text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded border border-blue-200 transition flex items-center gap-1 text-sm font-bold"
                                   title="Gerenciar Atributos">
                                    <i class="fas fa-tasks"></i> Gerenciar
                                </a>

                                {{-- Botão Excluir (Limpa todos os vínculos deste tipo) --}}
                                <form action="{{ route('inclusive-radar.type-attribute-assignments.destroy', $firstItem->type_id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('Isso removerá TODOS os atributos vinculados ao tipo {{ $typeName }}. Continuar?')"
                                            class="text-red-600 hover:bg-red-50 px-3 py-1.5 rounded border border-red-100 transition"
                                            title="Remover Tudo">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="py-12 text-center text-gray-400 italic">
                            <i class="fas fa-layer-group text-3xl mb-2 block"></i>
                            Nenhum tipo de recurso possui atributos vinculados ainda.
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
