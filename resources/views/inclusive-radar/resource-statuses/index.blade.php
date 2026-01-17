<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Status do Sistema</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-8">
<div class="max-w-6xl mx-auto bg-white p-6 rounded shadow">

    <h1 class="text-2xl font-bold mb-6">
        Status do Sistema
    </h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <table class="w-full border border-gray-300">
        <thead class="bg-gray-200">
        <tr>
            <th class="border p-2 text-left">Código</th>
            <th class="border p-2 text-left">Nome</th>
            <th class="border p-2 text-center">Tecnologia Assistiva</th>
            <th class="border p-2 text-center">Material Educacional</th>
            <th class="border p-2 text-center">Bloq. Empréstimo</th>
            <th class="border p-2 text-center">Ativo</th>
            <th class="border p-2 text-center">Ações</th>
        </tr>
        </thead>

        <tbody>
        @foreach($statuses as $status)
            <tr class="hover:bg-gray-50">

                <td class="border p-2 font-mono text-sm text-gray-600">
                    {{ $status->code }}
                </td>

                <td class="border p-2">
                    {{ $status->name }}
                </td>

                <td class="border p-2 text-center">
                    {{ $status->for_assistive_technology ? '✔️' : '—' }}
                </td>

                <td class="border p-2 text-center">
                    {{ $status->for_educational_material ? '✔️' : '—' }}
                </td>

                <td class="border p-2 text-center">
                    {{ $status->blocks_loan ? 'Sim' : 'Não' }}
                </td>

                <td class="border p-2 text-center">
                    <span class="{{ $status->is_active ? 'text-green-600' : 'text-red-600' }}">
                        {{ $status->is_active ? 'Ativo' : 'Inativo' }}
                    </span>
                </td>

                <td class="border p-2 text-center space-x-2">

                    {{-- Editar --}}
                    <a href="{{ route('inclusive-radar.resource-statuses.edit', $status) }}"
                       class="bg-yellow-500 text-white px-3 py-1 rounded">
                        Editar
                    </a>

                    {{-- Ativar / Desativar --}}
                    <form
                        action="{{ route('inclusive-radar.resource-statuses.toggle-active', $status) }}"
                        method="POST"
                        class="inline"
                    >
                        @csrf
                        @method('PATCH')

                        <button
                            class="{{ $status->is_active
                                ? 'bg-gray-500'
                                : 'bg-green-600'
                            }} text-white px-3 py-1 rounded">
                            {{ $status->is_active ? 'Desativar' : 'Ativar' }}
                        </button>
                    </form>

                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <p class="text-sm text-gray-500 mt-4">
        ⚠️ Os status do sistema são definidos internamente.
        Você pode apenas editar nomes, descrições e ativação.
    </p>

</div>
</body>
</html>
