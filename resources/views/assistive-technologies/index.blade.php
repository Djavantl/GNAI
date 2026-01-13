<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tecnologias Assistivas - GNAI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-8">

<div class="max-w-6xl mx-auto">

    {{-- Cabeçalho Estilo GNAI --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Tecnologias Assistivas</h1>
            <p class="text-gray-600">Gerenciamento de periféricos, softwares e equipamentos de acessibilidade.</p>
        </div>

        <a href="{{ route('assistive-technologies.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded shadow transition font-bold flex items-center gap-2">
            <i class="fas fa-plus-circle"></i>
            Nova Tecnologia
        </a>
    </div>

    {{-- Mensagem de Sucesso --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-200 text-green-800 p-4 rounded mb-6 flex items-center gap-3 shadow-sm">
            <i class="fas fa-check-circle text-green-600"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- Tabela Estilo GNAI --}}
    <div class="bg-white p-6 rounded shadow border-t-4 border-blue-600">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                <tr class="border-b-2 border-gray-100">
                    <th class="py-3 px-4 font-bold text-gray-700">Equipamento / Tipo</th>
                    <th class="py-3 px-4 font-bold text-gray-700 text-center">Patrimônio</th>
                    <th class="py-3 px-4 font-bold text-gray-700 text-center">Qtd</th>
                    <th class="py-3 px-4 font-bold text-gray-700 text-center">Status</th>
                    <th class="py-3 px-4 font-bold text-gray-700 text-center">Ativo</th>
                    <th class="py-3 px-4 font-bold text-gray-700 text-right">Ações</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                @forelse($technologies as $tech)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-4 px-4 align-middle">
                            <div class="flex items-center gap-3">
                                <div class="bg-blue-100 text-blue-600 p-2 rounded">
                                    <i class="fas fa-laptop-medical"></i>
                                </div>
                                <div>
                                    <span class="font-bold text-gray-900 block">{{ $tech->name }}</span>
                                    <span class="text-xs text-gray-500 uppercase tracking-wider">{{ $tech->type ?: 'Não definido' }}</span>
                                </div>
                            </div>
                        </td>

                        <td class="py-4 px-4 text-center align-middle font-mono text-sm text-gray-600">
                            {{ $tech->asset_code ?: 'N/A' }}
                        </td>

                        <td class="py-4 px-4 text-center align-middle">
                            <span class="font-semibold text-gray-700">{{ $tech->quantity }}</span>
                        </td>

                        <td class="py-4 px-4 text-center align-middle">
                            <span class="bg-amber-50 text-amber-700 px-3 py-1 rounded-full text-xs font-bold border border-amber-100">
                                {{ $tech->status?->name ?? 'Sem Status' }}
                            </span>
                        </td>

                        <td class="py-4 px-4 text-center align-middle">
                            @if($tech->is_active)
                                <span class="text-green-600 bg-green-50 px-2 py-1 rounded text-xs font-bold border border-green-100">SIM</span>
                            @else
                                <span class="text-red-600 bg-red-50 px-2 py-1 rounded text-xs font-bold border border-red-100">NÃO</span>
                            @endif
                        </td>

                        <td class="py-4 px-4 text-right align-middle">
                            <div class="flex justify-end gap-2">
                                {{-- Botão Editar --}}
                                <a href="{{ route('assistive-technologies.edit', $tech) }}"
                                   class="text-blue-600 hover:bg-blue-50 px-3 py-1 rounded transition border border-blue-100 text-sm font-semibold"
                                   title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>

                                {{-- Botão Ativar/Desativar --}}
                                <form action="{{ route('assistive-technologies.toggle', $tech) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="{{ $tech->is_active ? 'text-amber-600 border-amber-100 hover:bg-amber-50' : 'text-green-600 border-green-100 hover:bg-green-50' }} px-3 py-1 rounded transition border text-sm font-semibold"
                                            title="{{ $tech->is_active ? 'Desativar' : 'Ativar' }}">
                                        <i class="fas {{ $tech->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                    </button>
                                </form>

                                {{-- Botão Excluir --}}
                                <form action="{{ route('assistive-technologies.destroy', $tech) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('Excluir esta tecnologia permanentemente?')"
                                            class="text-red-600 hover:bg-red-50 px-3 py-1 rounded transition border border-red-100 text-sm font-semibold"
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
                            <i class="fas fa-microchip text-4xl mb-4 block opacity-20"></i>
                            <p class="text-lg">Nenhuma tecnologia encontrada.</p>
                            <p class="text-sm">Clique em "Nova Tecnologia" para começar o inventário.</p>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Rodapé Informativo Estilo GNAI --}}
    <div class="mt-8 bg-blue-50 p-4 rounded border border-blue-100 flex items-center gap-4">
        <div class="bg-blue-600 text-white p-3 rounded-full shadow-lg">
            <i class="fas fa-info-circle text-xl"></i>
        </div>
        <div>
            <h3 class="font-bold text-blue-800 text-sm uppercase tracking-wider">Gestão de Equipamentos</h3>
            <p class="text-sm text-blue-700">
                Tecnologias ativas aparecem na busca pública para os alunos. Equipamentos que requerem treinamento devem ter essa nota destacada.
            </p>
        </div>
    </div>
</div>

</body>
</html>
