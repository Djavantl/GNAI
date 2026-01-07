<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Materiais Pedagógicos Acessíveis</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-8">
<div class="max-w-5xl mx-auto bg-white p-6 rounded shadow">

    <h1 class="text-2xl font-bold mb-6">
        Materiais Pedagógicos Acessíveis
    </h1>

    <a href="{{ route('accessible-educational-materials.create') }}"
       class="bg-blue-500 text-white px-4 py-2 rounded">
        Novo Material
    </a>

    <table class="w-full border mt-4">
        <thead class="bg-gray-200">
        <tr>
            <th class="border p-2">Título</th>
            <th class="border p-2">Patrimônio</th>
            <th class="border p-2">Tipo</th>
            <th class="border p-2">Status</th>
            <th class="border p-2">Ativo</th>
            <th class="border p-2">Ações</th>
        </tr>
        </thead>

        <tbody>
        @forelse($materials as $material)
            <tr>
                <td class="border p-2">
                    {{ $material->title }}
                </td>

                <td class="border p-2">
                    {{ $material->asset_code }}
                </td>

                <td class="border p-2">
                    {{ $material->type }}
                </td>

                <td class="border p-2">
                    {{ $material->accessibleEducationalMaterialStatus?->name }}
                </td>

                <td class="border p-2 text-center">
                    {{ $material->is_active ? 'Sim' : 'Não' }}
                </td>

                <td class="border p-2 space-x-2">
                    <a href="{{ route('accessible-educational-materials.edit', $material) }}"
                       class="bg-yellow-500 text-white px-3 py-1 rounded">
                        Editar
                    </a>

                    <form action="{{ route('accessible-educational-materials.destroy', $material) }}"
                          method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button class="bg-red-500 text-white px-3 py-1 rounded"
                                onclick="return confirm('Deseja excluir?')">
                            Excluir
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6"
                    class="text-center p-4 text-gray-500">
                    Nenhum material cadastrado
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

</div>
</body>
</html>
