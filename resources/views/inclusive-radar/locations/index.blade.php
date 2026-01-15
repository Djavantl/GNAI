<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pontos de Referência - Radar Inclusivo</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-4 md:p-8">
<div class="max-w-7xl mx-auto bg-white p-6 rounded-xl shadow-lg">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 border-b pb-4 gap-4">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            <span class="bg-amber-500 text-white p-2 rounded-lg">🏢</span>
            Pontos de Referência (Locations)
        </h1>
        <a href="{{ route('inclusive-radar.locations.create') }}"
           class="bg-amber-600 hover:bg-amber-700 text-white px-6 py-2.5 rounded-lg font-bold shadow-md transition-all flex items-center gap-2">
            <span>➕</span> Novo Ponto
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
        <table class="w-full text-left border-collapse bg-white">
            <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
                <th class="p-4 font-bold text-gray-600 text-sm uppercase">Nome / Prédio</th>
                <th class="p-4 font-bold text-gray-600 text-sm uppercase">Instituição (Campus)</th>
                <th class="p-4 font-bold text-gray-600 text-sm uppercase">Tipo</th>
                <th class="p-4 font-bold text-gray-600 text-sm uppercase text-center">Status</th>
                <th class="p-4 font-bold text-gray-600 text-sm uppercase text-right">Ações</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($locations as $loc)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4 font-semibold text-gray-800">
                        {{ $loc->name }}
                        <div class="text-[10px] text-gray-400 font-mono mt-1">{{ $loc->latitude }}, {{ $loc->longitude }}</div>
                    </td>
                    <td class="p-4 text-gray-600 text-sm italic">{{ $loc->institution->name }}</td>
                    <td class="p-4">
                        <span class="text-[11px] px-2 py-1 bg-gray-100 text-gray-500 rounded font-bold uppercase border">
                            {{ $loc->type ?? 'Geral' }}
                        </span>
                    </td>
                    <td class="p-4 text-center">
                        <form action="{{ route('inclusive-radar.locations.toggle-active', $loc) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="inline-flex items-center gap-1.5">
                                <div class="w-2.5 h-2.5 rounded-full {{ $loc->is_active ? 'bg-green-500' : 'bg-red-500' }}"></div>
                                <span class="text-xs font-bold {{ $loc->is_active ? 'text-green-700' : 'text-red-700' }}">
                                    {{ $loc->is_active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </button>
                        </form>
                    </td>
                    <td class="p-4 text-right space-x-2">
                        <a href="{{ route('inclusive-radar.locations.edit', $loc) }}" class="text-amber-600 hover:text-amber-800 font-bold text-sm">Editar</a>
                        <form action="{{ route('inclusive-radar.locations.destroy', $loc) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-sm" onclick="return confirm('Excluir este local?')">Excluir</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="p-10 text-center text-gray-400 italic">Nenhum ponto de referência cadastrado.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
