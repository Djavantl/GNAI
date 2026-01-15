<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorias de Barreiras - Radar Inclusivo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-8">

<div class="max-w-6xl mx-auto">
    {{-- Cabeçalho --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Categorias de Barreiras</h1>
            <p class="text-gray-600">Classificação para o mapeamento de acessibilidade.</p>
        </div>

        <a href="{{ route('inclusive-radar.barrier-categories.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded shadow transition font-bold flex items-center gap-2">
            <i class="fas fa-plus-circle"></i>
            Nova Categoria
        </a>
    </div>

    {{-- Feedback --}}
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
                    <th class="py-3 px-4 font-bold text-gray-700">Nome da Categoria</th>
                    <th class="py-3 px-4 font-bold text-gray-700">Descrição</th>
                    <th class="py-3 px-4 font-bold text-gray-700 text-center">Barreiras Vinculadas</th>
                    <th class="py-3 px-4 font-bold text-gray-700 text-center">Status</th>
                    <th class="py-3 px-4 font-bold text-gray-700 text-right">Ações</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                @forelse($categories as $category)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-4 px-4 align-middle">
                            <span class="font-bold text-gray-900">{{ $category->name }}</span>
                        </td>
                        <td class="py-4 px-4 text-sm text-gray-600 italic">
                            {{ Str::limit($category->description, 60) ?: 'Sem descrição' }}
                        </td>
                        <td class="py-4 px-4 text-center align-middle font-semibold text-blue-600">
                            {{ $category->barriers_count ?? $category->barriers->count() }}
                        </td>
                        <td class="py-4 px-4 text-center align-middle">
                            @if($category->is_active)
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold border border-green-200">Ativo</span>
                            @else
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold border border-red-200">Inativo</span>
                            @endif
                        </td>
                        <td class="py-4 px-4 text-right align-middle">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('inclusive-radar.barrier-categories.edit', $category->id) }}"
                                   class="text-blue-600 hover:text-blue-800 p-2" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('inclusive-radar.barrier-categories.toggle-active', $category->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="{{ $category->is_active ? 'text-amber-600' : 'text-green-600' }} p-2"
                                            title="{{ $category->is_active ? 'Desativar' : 'Ativar' }}">
                                        <i class="fas {{ $category->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                    </button>
                                </form>

                                <form action="{{ route('inclusive-radar.barrier-categories.destroy', $category->id) }}" method="POST"
                                      onsubmit="return confirm('Tem certeza que deseja excluir? Esta ação não pode ser desfeita se houver barreiras vinculadas.');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 p-2">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-gray-500 italic">Nenhuma categoria cadastrada.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
