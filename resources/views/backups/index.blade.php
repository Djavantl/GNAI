<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Histórico de Backups - GNAI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-8">

<div class="max-w-6xl mx-auto">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Gerenciamento de Backups</h1>
            <p class="text-gray-600">Visualize e administre as cópias de segurança do banco de dados.</p>
        </div>

        <form action="{{ route('backups.store') }}" method="POST">
            @csrf
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded shadow transition font-bold flex items-center gap-2">
                <i class="fas fa-plus-circle"></i>
                Gerar Novo Backup
            </button>
        </form>
    </div>

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
                    <th class="py-3 px-4 font-bold text-gray-700">Arquivo ZIP</th>
                    <th class="py-3 px-4 font-bold text-gray-700 text-center">Tamanho</th>
                    <th class="py-3 px-4 font-bold text-gray-700">Criado em</th>
                    <th class="py-3 px-4 font-bold text-gray-700">Responsável</th>
                    <th class="py-3 px-4 font-bold text-gray-700 text-right">Ações</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                @forelse($backups as $backup)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-4 px-4 align-middle">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-file-archive text-amber-500 text-xl"></i>
                                <span class="font-medium text-gray-900">{{ $backup->file_name }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-4 text-center align-middle">
                                <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-xs font-bold border border-blue-100">
                                    {{ $backup->size }}
                                </span>
                        </td>
                        <td class="py-4 px-4 text-gray-600 align-middle">
                            {{ $backup->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="py-4 px-4 align-middle">
                            <div class="flex items-center gap-2">
                                    <span class="text-sm font-semibold px-2 py-1 bg-gray-100 rounded text-gray-600">
                                        {{ $backup->user->name ?? 'Sistema' }}
                                    </span>
                            </div>
                        </td>
                        <td class="py-4 px-4 text-right align-middle">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('backups.edit', $backup->id) }}" class="text-blue-600 hover:bg-blue-50 px-3 py-1 rounded transition border border-blue-100 text-sm font-semibold">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <a href="{{ route('backups.download', $backup->id) }}" class="text-green-600 hover:bg-green-50 px-3 py-1 rounded transition border border-green-100 text-sm font-semibold">
                                    <i class="fas fa-download"></i>
                                </a>

                                <form action="{{ route('backups.destroy', $backup->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('Excluir este backups? Esta ação não pode ser desfeita.')"
                                            class="text-red-600 hover:bg-red-50 px-3 py-1 rounded transition border border-red-100 text-sm font-semibold">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-gray-400">
                            <i class="fas fa-database text-4xl mb-4 block opacity-20"></i>
                            <p class="text-lg">Nenhum backup encontrado.</p>
                            <p class="text-sm">Clique em "Gerar Novo Backup" para começar.</p>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($backups->hasPages())
            <div class="mt-6 pt-4 border-t border-gray-100">
                {{ $backups->links() }}
            </div>
        @endif
    </div>

    <div class="mt-8 bg-blue-50 p-4 rounded border border-blue-100">
        <h3 class="font-bold text-blue-800 text-sm uppercase flex items-center gap-2 mb-1">
            <i class="fas fa-info-circle"></i>
            Informações Importantes
        </h3>
        <p class="text-sm text-blue-700">
            Os backups automáticos são gerados na pasta <code class="bg-blue-100 px-1 rounded font-bold">storage/app/GNAI</code>.
            Certifique-se de baixar arquivos importantes para um local externo periodicamente.
        </p>
    </div>
</div>

</body>
</html>
