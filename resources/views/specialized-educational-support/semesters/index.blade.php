<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de Semestres</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-8">
<div class="max-w-6xl mx-auto bg-white p-6 rounded shadow">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Semestres</h1>
        <a href="{{ route('specialized-educational-support.semesters.create') }}"
           class="bg-blue-500 text-white px-4 py-2 rounded">
            Novo Semestre
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 mb-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    <table class="w-full text-left border-collapse">
        <thead>
        <tr class="border-b">
            <th class="p-2">Ano</th>
            <th class="p-2">Período</th>
            <th class="p-2">Rótulo</th>
            <th class="p-2">Atual</th>
            <th class="p-2">Ações</th>
        </tr>
        </thead>
        <tbody>
        @foreach($semesters as $semester)
            <tr class="border-b hover:bg-gray-50">
                <td class="p-2">{{ $semester->year }}</td>
                <td class="p-2">{{ $semester->term }}</td>
                <td class="p-2">{{ $semester->label }}</td>
                <td class="p-2">
                    @if($semester->is_current)
                        <span class="text-green-600 font-semibold">Sim</span>
                    @else
                        <span class="text-gray-500">Não</span>
                    @endif
                </td>
                <td class="p-2 flex gap-3">
                    <a href="{{ route('specialized-educational-support.semesters.edit', $semester) }}"
                       class="text-orange-500">Editar</a>

                    @if(!$semester->is_current)
                        <form action="{{ route('specialized-educational-support.semesters.setCurrent', $semester) }}"
                              method="POST">
                            @csrf
                            @method('PATCH')
                            <button class="text-blue-500">Definir Atual</button>
                        </form>
                    @endif

                    <form action="{{ route('specialized-educational-support.semesters.destroy', $semester) }}"
                          method="POST"
                          onsubmit="return confirm('Excluir semestre?')">
                        @csrf
                        @method('DELETE')
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
