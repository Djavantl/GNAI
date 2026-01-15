<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barreiras Identificadas - Radar Inclusivo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-8">

<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Mapa de Barreiras</h1>
            <p class="text-gray-600">Contribuições da comunidade para uma instituição mais acessível.</p>
        </div>

        <a href="{{ route('inclusive-radar.barriers.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded shadow transition font-bold flex items-center gap-2">
            <i class="fas fa-bullhorn"></i>
            Relatar Barreira
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-200 text-green-800 p-4 rounded mb-6 flex items-center gap-3">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white p-6 rounded shadow border-t-4 border-orange-500">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                <tr class="border-b-2 border-gray-100">
                    <th class="py-3 px-4 font-bold text-gray-700">Barreira / Categoria</th>
                    <th class="py-3 px-4 font-bold text-gray-700">Localização</th>
                    <th class="py-3 px-4 font-bold text-gray-700 text-center">Prioridade</th>
                    <th class="py-3 px-4 font-bold text-gray-700 text-center">Relator</th>
                    <th class="py-3 px-4 font-bold text-gray-700 text-right">Ações</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                @forelse($barriers as $barrier)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-4 px-4">
                            <span class="font-bold text-gray-900 block">{{ $barrier->name }}</span>
                            <span class="text-xs text-blue-600 font-semibold">{{ $barrier->category->name }}</span>
                        </td>
                        <td class="py-4 px-4 text-sm text-gray-600">
                            <i class="fas fa-map-marker-alt text-red-500 mr-1"></i>
                            {{ $barrier->location?->name ?? 'Ponto exato no mapa / Digital' }}
                        </td>
                        <td class="py-4 px-4 text-center">
                            @php
                                $priorityColors = [
                                    'Baixa' => 'bg-blue-100 text-blue-700',
                                    'Média' => 'bg-yellow-100 text-yellow-700',
                                    'Alta' => 'bg-orange-100 text-orange-700',
                                    'Crítica' => 'bg-red-100 text-red-700',
                                ];
                            @endphp
                            <span class="{{ $priorityColors[$barrier->priority] ?? 'bg-gray-100' }} px-3 py-1 rounded-full text-xs font-bold">
                                {{ $barrier->priority }}
                            </span>
                        </td>
                        <td class="py-4 px-4 text-center">
                            <span class="text-sm font-medium text-gray-700 block">{{ $barrier->display_name }}</span>
                            <span class="text-[10px] uppercase text-gray-400 italic">{{ $barrier->reporter_role ?: 'Visitante' }}</span>
                        </td>
                        <td class="py-4 px-4 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('inclusive-radar.barriers.edit', $barrier->id) }}" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('inclusive-radar.barriers.destroy', $barrier->id) }}" method="POST" onsubmit="return confirm('Excluir este relato?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-gray-500 italic">Nenhuma barreira relatada até o momento.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $barriers->links() }}
        </div>
    </div>
</div>
</body>
</html>
