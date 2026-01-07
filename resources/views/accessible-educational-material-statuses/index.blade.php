<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Status de Materiais Pedagógicos Acessíveis</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-6">
        Status de Materiais Pedagógicos Acessíveis
    </h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('accessible-educational-material-statuses.create') }}"
       class="bg-blue-500 text-white px-4 py-2 rounded mb-4 inline-block">
        Novo Status
    </a>

    <table class="w-full border border-gray-300 mt-4">
        <thead class="bg-gray-200">
        <tr>
            <th class="border p-2 text-left">Nome</th>
            <th class="border p-2 text-left">Descrição</th>
            <th class="border p-2 text-center">Ativo</th>
            <th class="border p-2 text-center">Ações</th>
        </tr>
        </thead>
        <tbody>
        @forelse($statuses as $status)
            <tr class="hover:bg-gray-50">
                <td class="border p-2">{{ $status->name }}</td>
                <td class="border p-2">{{ $status->description }}</td>
                <td class="border p-2 text-center">
                    {{ $status->is_active ? 'Sim' : 'Não' }}
                </td>
                <td class="border p-2 text-center space-x-2">
                    <a href="{{ route('accessible-educational-material-statuses.edit', $status) }}"
                       class="bg-yellow-500 text-white px-3 py-1 rounded">
                        Editar
                    </a>

                    @if($status->is_active)
                        <form action="{{ route('accessible-educational-material-statuses.deactivate', $status) }}"
                              method="POST"
                              class="inline">
                            @csrf
                            @method('PATCH')
                            <button class="bg-gray-500 text-white px-3 py-1 rounded">
                                Desativar
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('accessible-educational-material-statuses.destroy', $status) }}"
                          method="POST"
                          class="inline"
                          onsubmit="return confirm('Tem certeza que deseja excluir?')">
                        @csrf
                        @method('DELETE')
                        <button class="bg-red-500 text-white px-3 py-1 rounded">
                            Excluir
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="p-4 text-center text-gray-500">
                    Nenhum status cadastrado.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
</body>
</html>
