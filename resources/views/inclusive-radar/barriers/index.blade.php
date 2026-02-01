<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa de Barreiras - Radar Inclusivo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-4 md:p-8">

<div class="max-w-7xl mx-auto">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
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
        <div class="bg-green-100 border border-green-200 text-green-800 p-4 rounded mb-6 flex items-center gap-3 shadow-sm">
            <i class="fas fa-check-circle text-green-600"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white p-6 rounded shadow border-t-4 border-orange-500">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                <tr class="border-b-2 border-gray-100 uppercase text-[11px] tracking-wider">
                    <th class="py-3 px-4 font-bold text-gray-700">Barreira / Categoria</th>
                    <th class="py-3 px-4 font-bold text-gray-700">Localização</th>
                    <th class="py-3 px-4 font-bold text-gray-700 text-center">Prioridade</th>
                    <th class="py-3 px-4 font-bold text-gray-700 text-center">Status</th>
                    <th class="py-3 px-4 font-bold text-gray-700 text-center">Relator</th>
                    <th class="py-3 px-4 font-bold text-gray-700 text-right">Ações</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                @forelse($barriers as $barrier)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-4 px-4 align-middle">
                            <div class="flex items-center gap-3">
                                @php
                                    $firstImage = $barrier->inspections->first()?->images->first();
                                @endphp
                                <div class="w-12 h-12 flex-shrink-0 bg-gray-100 rounded border border-gray-200 overflow-hidden flex items-center justify-center">
                                    @if($firstImage)
                                        <img src="{{ asset('storage/' . $firstImage->path) }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="fas fa-image text-gray-300"></i>
                                    @endif
                                </div>
                                <div>
                                    <span class="font-bold text-gray-900 block leading-tight">{{ $barrier->name }}</span>
                                    <span class="text-[10px] text-blue-600 uppercase tracking-wider font-semibold">
                                        {{ $barrier->category->name }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <td class="py-4 px-4 text-sm text-gray-600 align-middle">
                            <div class="flex flex-col">
                                <span class="font-medium text-gray-800">
                                    <i class="fas fa-map-marker-alt text-red-500 mr-1"></i>
                                    {{ $barrier->location?->name ?? 'Local não definido' }}
                                </span>
                                <span class="text-[10px] text-gray-400 uppercase">
                                    {{ $barrier->institution->short_name ?? $barrier->institution->name }}
                                </span>
                            </div>
                        </td>

                        <td class="py-4 px-4 text-center align-middle">
                            @php
                                $priority = $barrier->priority;
                                $colorClass = match($priority->value ?? $priority) {
                                    'low'      => 'bg-blue-100 text-blue-700 border-blue-200',
                                    'medium'   => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                    'high'     => 'bg-orange-100 text-orange-700 border-orange-200',
                                    'critical' => 'bg-red-100 text-red-700 border-red-200',
                                    default    => 'bg-gray-100 text-gray-700 border-gray-200',
                                };
                            @endphp
                            <span class="{{ $colorClass }} px-2 py-0.5 rounded text-[10px] font-bold uppercase border">
                                {{ is_object($priority) && method_exists($priority, 'label') ? $priority->label() : ucfirst($priority->value ?? $priority) }}
                            </span>
                        </td>

                        <td class="py-4 px-4 text-center align-middle">
                            <span class="bg-white text-gray-600 border-gray-300 px-2 py-1 rounded text-[10px] font-bold border uppercase shadow-sm">
                                {{ $barrier->status->name }}
                            </span>
                        </td>

                        <td class="py-4 px-4 text-center align-middle">
                            <span class="text-sm font-semibold text-gray-700 block leading-tight">
                                @if($barrier->is_anonymous)
                                    <i class="fas fa-user-secret text-gray-400 mr-1"></i> Anônimo
                                @else
                                    {{ $barrier->registeredBy->name ?? 'Sistema' }}
                                @endif
                            </span>
                            @if($barrier->affected_person_role)
                                <span class="text-[9px] uppercase text-gray-400 font-bold italic">{{ $barrier->affected_person_role }}</span>
                            @endif
                        </td>

                        <td class="py-4 px-4 text-right align-middle">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('inclusive-radar.barriers.edit', $barrier) }}"
                                   class="text-blue-600 hover:bg-blue-50 px-3 py-1 rounded transition border border-blue-100 text-sm"
                                   title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('inclusive-radar.barriers.toggle', $barrier) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="{{ $barrier->is_active ? 'text-amber-600 border-amber-100 hover:bg-amber-50' : 'text-green-600 border-green-100 hover:bg-green-50' }} px-3 py-1 rounded transition border text-sm">
                                        <i class="fas {{ $barrier->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                    </button>
                                </form>

                                <form action="{{ route('inclusive-radar.barriers.destroy', $barrier) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('Deseja excluir este relato permanentemente?')"
                                            class="text-red-600 hover:bg-red-50 px-3 py-1 rounded transition border border-red-100 text-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-gray-400">
                            <i class="fas fa-check-circle text-4xl mb-3"></i>
                            <p class="text-lg font-semibold italic">Nenhuma barreira identificada até o momento.</p>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($barriers, 'hasPages') && $barriers->hasPages())
            <div class="mt-6">
                {{ $barriers->links() }}
            </div>
        @endif
    </div>
</div>

</body>
</html>
