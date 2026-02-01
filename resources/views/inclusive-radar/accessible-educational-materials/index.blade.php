<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Materiais Pedagógicos Acessíveis - GNAI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-4 md:p-8">

<div class="max-w-7xl mx-auto">

    {{-- Cabeçalho --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Materiais Pedagógicos Acessíveis (MPA)</h1>
            <p class="text-gray-600">Gestão de recursos didáticos, livros e jogos adaptados.</p>
        </div>

        <a href="{{ route('inclusive-radar.accessible-educational-materials.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded shadow transition font-bold flex items-center gap-2">
            <i class="fas fa-plus-circle"></i>
            Novo Material
        </a>
    </div>

    {{-- Feedback de Mensagens e Erros de Validação (Bloqueio de Exclusão) --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-200 text-green-800 p-4 rounded mb-6 flex items-center gap-3 shadow-sm">
            <i class="fas fa-check-circle text-green-600"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-200 text-red-800 p-4 rounded mb-6 shadow-sm">
            <div class="flex items-center gap-2 font-bold mb-1">
                <i class="fas fa-exclamation-triangle"></i> Atenção:
            </div>
            <ul class="list-disc ml-5 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Tabela Estilo TA --}}
    <div class="bg-white p-6 rounded shadow border-t-4 border-blue-600">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                <tr class="border-b-2 border-gray-100 uppercase text-[11px] tracking-wider">
                    <th class="py-3 px-4 font-bold text-gray-700">Material / Tipo</th>
                    <th class="py-3 px-4 font-bold text-gray-700 text-center">Natureza</th>
                    <th class="py-3 px-4 font-bold text-gray-700 text-center">Estoque (Disp. / Total)</th>
                    <th class="py-3 px-4 font-bold text-gray-700 text-center">Status</th>
                    <th class="py-3 px-4 font-bold text-gray-700 text-center">Ativo</th>
                    <th class="py-3 px-4 font-bold text-gray-700 text-right">Ações</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                @forelse($materials as $material)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-4 px-4 align-middle">
                            <div class="flex items-center gap-3">
                                {{-- Thumbnail da Imagem (Pega a primeira imagem da primeira inspeção) --}}
                                @php
                                    $firstImage = $material->inspections->flatMap->images->first();
                                @endphp
                                <div class="w-12 h-12 flex-shrink-0 bg-gray-100 rounded border border-gray-200 overflow-hidden flex items-center justify-center">
                                    @if($firstImage)
                                        <img src="{{ asset('storage/' . $firstImage->path) }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="fas {{ $material->type?->is_digital ? 'fa-file-download' : 'fa-book' }} text-gray-400"></i>
                                    @endif
                                </div>
                                <div>
                                    {{-- CORREÇÃO: title -> name --}}
                                    <span class="font-bold text-gray-900 block leading-tight">{{ $material->name }}</span>
                                    <span class="text-[10px] text-gray-500 uppercase tracking-wider font-semibold">
                                        {{ $material->type?->name ?: 'Didático' }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <td class="py-4 px-4 text-center align-middle">
                            @if($material->type?->is_digital)
                                <span class="bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded text-[9px] font-extrabold uppercase border border-indigo-200">Digital</span>
                            @else
                                <span class="bg-amber-100 text-amber-700 px-2 py-0.5 rounded text-[9px] font-extrabold uppercase border border-amber-200">Físico</span>
                            @endif
                        </td>

                        <td class="py-4 px-4 text-center align-middle">
                            @if($material->type?->is_digital)
                                <span class="text-blue-600 text-[10px] font-bold uppercase tracking-tighter bg-blue-50 px-2 py-1 rounded border border-blue-100">
                                    <i class="fas fa-infinity mr-1"></i> Ilimitado
                                </span>
                            @else
                                <div class="flex flex-col items-center">
                                    <div class="flex items-center gap-1 mb-1">
                                        <span class="px-2 py-0.5 rounded text-xs font-bold border {{ ($material->quantity_available ?? 0) > 0 ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' }}">
                                            {{ $material->quantity_available ?? 0 }}
                                        </span>
                                        <span class="text-gray-400 text-xs">/</span>
                                        <span class="text-gray-800 font-semibold text-xs">{{ $material->quantity ?? 0 }}</span>
                                    </div>
                                    <span class="text-[9px] font-mono text-gray-400 leading-none uppercase">
                                        {{ $material->asset_code ?: 'S/ PATRIMÔNIO' }}
                                    </span>
                                </div>
                            @endif
                        </td>

                        <td class="py-4 px-4 text-center align-middle">
                            @php
                                $isUnavailable = !$material->type?->is_digital && (($material->quantity_available ?? 0) <= 0);
                            @endphp
                            <span class="{{ $isUnavailable ? 'bg-red-100 text-red-700 border-red-200' : 'bg-gray-100 text-gray-600 border-gray-200' }} px-2 py-1 rounded-full text-[10px] font-bold border uppercase">
                                {{ $isUnavailable ? 'Esgotado' : ($material->resourceStatus?->name ?? 'Disponível') }}
                            </span>
                        </td>

                        <td class="py-4 px-4 text-center align-middle">
                            @if($material->is_active)
                                <span class="text-green-600 bg-green-50 px-2 py-1 rounded text-[10px] font-bold border border-green-100">SIM</span>
                            @else
                                <span class="text-red-600 bg-red-50 px-2 py-1 rounded text-[10px] font-bold border border-red-100">NÃO</span>
                            @endif
                        </td>

                        <td class="py-4 px-4 text-right align-middle">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('inclusive-radar.accessible-educational-materials.edit', $material) }}"
                                   class="text-blue-600 hover:bg-blue-600 hover:text-white px-3 py-1 rounded transition border border-blue-200 text-sm"
                                   title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('inclusive-radar.accessible-educational-materials.toggle', $material) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="{{ $material->is_active ? 'text-amber-600 border-amber-200 hover:bg-amber-600' : 'text-green-600 border-green-200 hover:bg-green-600' }} hover:text-white px-3 py-1 rounded transition border text-sm"
                                            title="{{ $material->is_active ? 'Ocultar' : 'Mostrar' }}">
                                        <i class="fas {{ $material->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                    </button>
                                </form>

                                <form action="{{ route('inclusive-radar.accessible-educational-materials.destroy', $material) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('Excluir este material permanentemente? Esta ação não pode ser desfeita e só será permitida se não houver empréstimos pendentes.')"
                                            class="text-red-600 hover:bg-red-600 hover:text-white px-3 py-1 rounded transition border border-red-200 text-sm"
                                            title="Excluir">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-gray-400">
                            <i class="fas fa-box-open text-4xl mb-3"></i>
                            <p class="text-lg font-semibold italic">Nenhum material pedagógico cadastrado.</p>
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
