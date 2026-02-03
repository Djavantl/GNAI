<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Recursos de Acessibilidade</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
<div class="max-w-5xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-6">Recursos de Acessibilidade</h1>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-6">
        <a href="{{ route('inclusive-radar.accessibility-features.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
            + Novo Recurso
        </a>
    </div>

    <table class="w-full border border-gray-200 rounded">
        <thead class="bg-gray-50 border-b border-gray-200">
        <tr>
            <th class="text-left p-3">Nome</th>
            <th class="text-left p-3">Descrição</th>
            <th class="text-center p-3">Ativo</th>
            <th class="text-center p-3">Ações</th>
        </tr>
        </thead>
        <tbody>
        @forelse($features as $feature)
            <tr class="border-b border-gray-200 hover:bg-gray-50">
                <td class="p-3">{{ $feature->name }}</td>
                <td class="p-3">{{ $feature->description }}</td>
                <td class="p-3 text-center">
                    @if($feature->is_active)
                        <span class="text-green-600 font-bold">Sim</span>
                    @else
                        <span class="text-red-600 font-bold">Não</span>
                    @endif
                </td>
                <td class="p-3 text-center flex justify-center gap-2">
                    <a href="{{ route('inclusive-radar.accessibility-features.edit', $feature) }}"
                       class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm">
                        Editar
                    </a>

                    <form action="{{ route('inclusive-radar.accessibility-features.toggle', $feature) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded text-sm">
                            {{ $feature->is_active ? 'Desativar' : 'Ativar' }}
                        </button>
                    </form>

                    <form action="{{ route('inclusive-radar.accessibility-features.destroy', $feature) }}" method="POST"
                          onsubmit="return confirm('Deseja realmente excluir este recurso?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">
                            Excluir
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="p-4 text-center text-gray-500">Nenhum recurso cadastrado.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
</body>
</html>
