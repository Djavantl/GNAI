<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de Pessoas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-6xl mx-auto bg-white p-6 rounded shadow">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Pessoas Cadastradas</h1>
            <a href="{{ route('people.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">Novo Cadastro</a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 mb-4 rounded">{{ session('success') }}</div>
        @endif

        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b">
                    <th class="p-2">Nome</th>
                    <th class="p-2">Documento</th>
                    <th class="p-2">Gênero</th>
                    <th class="p-2">E-mail</th>
                    <th class="p-2">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($people as $person)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-2">{{ $person->name }}</td>
                    <td class="p-2">{{ $person->document }}</td>
                    <td class="p-2">{{ \App\Models\Person::genderOptions()[$person->gender] ?? $person->gender }}</td>
                    <td class="p-2">{{ $person->email }}</td>
                    <td class="p-2 flex gap-2">
                        <a href="{{ route('people.edit', $person) }}" class="text-orange-500">Editar</a>
                        <form action="{{ route('people.destroy', $person) }}" method="POST" onsubmit="return confirm('Deletar?')">
                            @csrf @method('DELETE')
                            <button class="text-red-500">Excluir</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>