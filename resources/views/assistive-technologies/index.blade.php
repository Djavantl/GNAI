{{-- resources/views/assistive-technologies/index.blade.php --}}
    <!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Tecnologias Assistivas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
<div class="max-w-5xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-6">Tecnologias Assistivas</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('assistive-technologies.create') }}"
       class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 mb-4 inline-block">
        Cadastrar Nova Tecnologia
    </a>

    <table class="w-full border border-gray-300 rounded">
        <thead class="bg-gray-100">
        <tr class="text-center">
            <th class="border p-2">ID</th>
            <th class="border p-2">Nome</th>
            <th class="border p-2">Tipo</th>
            <th class="border p-2">Quantidade</th>
            <th class="border p-2">Status</th>
            <th class="border p-2">Ativo</th>
            <th class="border p-2">Ações</th>
        </tr>
        </thead>
        <tbody>
        @forelse($technologies as $tech)
            <tr class="text-center">
                <td class="border p-2">{{ $tech->id }}</td>
                <td class="border p-2">{{ $tech->name }}</td>
                <td class="border p-2">{{ $tech->type ?? '-' }}</td>
                <td class="border p-2">{{ $tech->quantity }}</td>
                <td class="border p-2">{{ $tech->status->name ?? '-' }}</td>
                <td class="border p-2">
                    @if($tech->is_active)
                        <span class="text-green-600 font-semibold">Sim</span>
                    @else
                        <span class="text-red-600 font-semibold">Não</span>
                    @endif
                </td>
                <td class="border p-2 space-x-2">
                    <a href="{{ route('assistive-technologies.edit', $tech) }}"
                       class="bg-yellow-400 text-white px-2 py-1 rounded hover:bg-yellow-500">
                        Editar
                    </a>

                    <form action="{{ route('assistive-technologies.toggle', $tech) }}" method="POST" class="inline-block">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="bg-gray-500 text-white px-2 py-1 rounded hover:bg-gray-600">
                            {{ $tech->is_active ? 'Desativar' : 'Ativar' }}
                        </button>
                    </form>

                    <form action="{{ route('assistive-technologies.destroy', $tech) }}" method="POST" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                onclick="return confirm('Deseja realmente remover esta tecnologia?')"
                                class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600">
                            Excluir
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="border p-2">Nenhuma tecnologia cadastrada.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
</body>
</html>
