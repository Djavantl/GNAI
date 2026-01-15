<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Instituições - Radar Inclusivo</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 p-4 md:p-12">
<div class="max-w-7xl mx-auto">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Instituições Base</h1>
            <p class="text-slate-500">Gerencie os locais centrais onde o radar de acessibilidade opera.</p>
        </div>
        <a href="{{ route('inclusive-radar.institutions.create') }}"
           class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg transition duration-200 gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nova Instituição
        </a>
    </div>

    {{-- Alertas de Sucesso --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded shadow-sm flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-green-900 font-bold">&times;</button>
        </div>
    @endif

    {{-- Tabela / Lista --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-slate-200">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-100 border-b border-slate-200">
            <tr>
                <th class="p-4 text-sm font-bold text-slate-600 uppercase">Instituição</th>
                <th class="p-4 text-sm font-bold text-slate-600 uppercase">Localização</th>
                <th class="p-4 text-sm font-bold text-slate-600 uppercase">Coordenadas</th>
                <th class="p-4 text-sm font-bold text-slate-600 uppercase text-center">Status</th>
                <th class="p-4 text-sm font-bold text-slate-600 uppercase text-right">Ações</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
            @forelse($institutions as $inst)
                <tr class="hover:bg-slate-50 transition">
                    <td class="p-4">
                        <div class="font-bold text-slate-800">{{ $inst->name }}</div>
                        <div class="text-xs text-slate-500">{{ $inst->short_name ?? 'Sem sigla' }}</div>
                    </td>
                    <td class="p-4 text-slate-600">
                        {{ $inst->city }} - {{ $inst->state }}
                    </td>
                    <td class="p-4 font-mono text-xs text-blue-600">
                        {{ number_format($inst->latitude, 5) }}, {{ number_format($inst->longitude, 5) }}
                    </td>
                    <td class="p-4 text-center">
                        <form action="{{ route('inclusive-radar.institutions.toggle-active', $inst) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="px-3 py-1 rounded-full text-xs font-bold {{ $inst->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $inst->is_active ? 'Ativo' : 'Inativo' }}
                            </button>
                        </form>
                    </td>
                    <td class="p-4 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('inclusive-radar.institutions.edit', $inst) }}"
                               class="p-2 text-slate-400 hover:text-blue-600 transition" title="Editar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>

                            <form action="{{ route('inclusive-radar.institutions.destroy', $inst) }}" method="POST"
                                  onsubmit="return confirm('Tem certeza que deseja excluir esta instituição? Todas as localizações vinculadas serão removidas.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-red-600 transition" title="Excluir">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="p-12 text-center">
                        <div class="flex flex-col items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-slate-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 7m0 13V7" />
                            </svg>
                            <p class="text-slate-500 font-medium">Nenhuma instituição cadastrada até o momento.</p>
                            <a href="{{ route('inclusive-radar.institutions.create') }}" class="mt-4 text-blue-600 font-bold hover:underline">Cadastrar a primeira agora</a>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Footer Info --}}
    <div class="mt-6 text-slate-400 text-sm flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>O status "Ativo" define qual instituição será usada como base padrão para novos relatos.</span>
    </div>
</div>
</body>
</html>
