<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciamento de Backups - GNAI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-8">

<div class="max-w-5xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Backups do Sistema</h1>
            <p class="text-gray-600">Gestão de cópias de segurança do banco de dados.</p>
        </div>

        <form action="{{ route('backups.store') }}" method="POST">
            @csrf
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded shadow transition font-bold flex items-center gap-2">
                <i class="fas fa-database"></i>
                Gerar Novo Backup
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm" role="alert">
            <p class="font-bold">Sucesso!</p>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm" role="alert">
            <p class="font-bold">Erro!</p>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white rounded shadow overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="p-4 font-semibold text-gray-700">Arquivo ZIP</th>
                <th class="p-4 font-semibold text-gray-700 text-center">Tamanho</th>
                <th class="p-4 font-semibold text-gray-700">Data e Hora</th>
                <th class="p-4 font-semibold text-gray-700">Responsável</th>
                <th class="p-4 font-semibold text-gray-700 text-right">Ações</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($backups as $backup)
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-file-archive text-amber-500 text-xl"></i>
                            <span class="font-medium text-blue-700">{{ $backup->file_name }}</span>
                        </div>
                    </td>
                    <td class="p-4 text-center">
                            <span class="bg-blue-50 text-blue-700 px-2 py-1 rounded-md text-xs font-bold border border-blue-100">
                                {{ $backup->size }}
                            </span>
                    </td>
                    <td class="p-4 text-gray-600">
                        {{ $backup->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="p-4 text-gray-600">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 bg-gray-200 rounded-full flex items-center justify-center text-xs font-bold text-gray-500">
                                {{ substr($backup->user->name ?? 'S', 0, 1) }}
                            </div>
                            {{ $backup->user->name ?? 'Sistema' }}
                        </div>
                    </td>
                    <td class="p-4 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('backups.download', $backup->id) }}"
                               class="text-green-600 hover:bg-green-50 px-3 py-1 rounded transition border border-green-200 text-sm font-semibold flex items-center gap-1">
                                <i class="fas fa-download"></i> Baixar
                            </a>

                            <form action="{{ route('backups.destroy', $backup->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        onclick="return confirm('Excluir este backups permanentemente?')"
                                        class="text-red-600 hover:bg-red-50 px-3 py-1 rounded transition border border-red-200 text-sm font-semibold flex items-center gap-1">
                                    <i class="fas fa-trash-alt"></i> Excluir
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="p-12 text-center text-gray-400 italic">
                        <i class="fas fa-info-circle text-2xl mb-2 block"></i>
                        Nenhum backup encontrado no histórico.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        @if($backups->hasPages())
            <div class="bg-gray-50 px-4 py-3 border-t border-gray-200">
                {{ $backups->links() }}
            </div>
        @endif
    </div>

    <div class="mt-6 p-4 bg-amber-50 border border-amber-100 rounded flex gap-3 items-start">
        <i class="fas fa-exclamation-triangle text-amber-600 mt-1"></i>
        <div class="text-sm text-amber-800">
            <strong>Dica de Segurança:</strong> Recomendamos baixar o arquivo de backup e armazená-lo em um local externo (nuvem ou HD externo) para garantir a recuperação em caso de falha no servidor.
        </div>
    </div>
</div>

</body>
</html>
