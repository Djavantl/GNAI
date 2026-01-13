<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Materiais Pedagógicos Acessíveis - GNAI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-8">

<div class="max-w-6xl mx-auto">

    {{-- Cabeçalho Estilo GNAI --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Materiais Pedagógicos Acessíveis</h1>
            <p class="text-gray-600">Gerenciamento de livros, jogos e recursos didáticos adaptados.</p>
        </div>

        <a href="{{ route('accessible-educational-materials.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded shadow transition font-bold flex items-center gap-2">
            <i class="fas fa-plus-circle"></i>
            Novo Material
        </a>
    </div>

    {{-- Mensagem de Sucesso --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-200 text-green-800 p-4 rounded mb-6 flex items-center gap-3 shadow-sm">
            <i class="fas fa-check-circle text-green-600"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- Tabela Estilo TA/Backup --}}
    <div class="bg-white p-6 rounded shadow border-t-4 border-blue-600">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                <tr class="border-b-2 border-gray-100">
                    <th class="py-3 px-4 font-bold text-gray-700">Título / Tipo</th>
                    <th class="py-3 px-4 font-bold text-gray-700 text-center">Patrimônio</th>
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
                                <div class="bg-blue-100 text-blue-600 p-2 rounded">
                                    <i class="fas fa-book-open"></i>
                                </div>
                                <div>
                                    <span class="font-bold text-gray-900 block">{{ $material->title }}</span>
                                    <span class="text-xs text-gray-500 uppercase tracking-wider">{{ $material->type }}</span>
                                </div>
                            </div>
                        </td>

                        <td class="py-4 px-4 text-center align-middle font-mono text-sm text-gray-600">
                            {{ $material->asset_code ?: 'N/A' }}
                        </td>

                        {{-- No seu index.blade.php, procure a linha do status e mude para: --}}
                        <td class="py-4 px-4 text-center align-middle">
                            <span class="bg-amber-50 text-amber-700 px-3 py-1 rounded-full text-xs font-bold border border-amber-100">
                                {{ $material->status?->name ?? 'Sem Status' }}
                            </span>
                        </td>

                        <td class="py-4 px-4 text-center align-middle">
                            @if($material->is_active)
                                <span class="text-green-600 bg-green-50 px-2 py-1 rounded text-xs font-bold">SIM</span>
                            @else
                                <span class="text-red-600 bg-red-50 px-2 py-1 rounded text-xs font-bold">NÃO</span>
                            @endif
                        </td>

                        <td class="py-4 px-4 text-right align-middle">
                            <div class="flex justify-end gap-2">
                                {{-- Botão Editar --}}
                                <a href="{{ route('accessible-educational-materials.edit', $material) }}"
                                   class="text-blue-600 hover:bg-blue-50 px-3 py-1 rounded transition border border-blue-100 text-sm font-semibold"
                                   title="Editar Material">
                                    <i class="fas fa-edit"></i>
                                </a>

                                {{-- Botão Excluir --}}
                                <form action="{{ route('accessible-educational-materials.destroy', $material) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('Excluir este material permanentemente?')"
                                            class="text-red-600 hover:bg-red-50 px-3 py-1 rounded transition border border-red-100 text-sm font-semibold"
                                            title="Excluir Material">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-gray-400">
                            <i class="fas fa-box-open text-4xl mb-4 block opacity-20"></i>
                            <p class="text-lg">Nenhum material cadastrado.</p>
                            <p class="text-sm">Clique em "Novo Material" para começar.</p>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginação --}}
        @if($materials->hasPages())
            <div class="mt-6 pt-4 border-t border-gray-100">
                {{ $materials->links() }}
            </div>
        @endif
    </div>

    {{-- Rodapé Informativo --}}
    <div class="mt-8 bg-blue-50 p-4 rounded border border-blue-100 flex items-center gap-4">
        <div class="bg-blue-600 text-white p-3 rounded-full shadow-lg">
            <i class="fas fa-info-circle text-xl"></i>
        </div>
        <div>
            <h3 class="font-bold text-blue-800 text-sm uppercase tracking-wider">Gestão de Acervo</h3>
            <p class="text-sm text-blue-700">
                Lembre-se de vincular os <b>Recursos de Acessibilidade</b> em cada material para facilitar a busca pelos alunos.
            </p>
        </div>
    </div>
</div>

</body>
</html>
