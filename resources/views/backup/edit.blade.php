<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Informações do Backup - GNAI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-8">

<div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
    <div class="flex items-center gap-3 mb-6">
        <i class="fas fa-edit text-2xl text-blue-600"></i>
        <h1 class="text-2xl font-bold text-gray-800">Editar Registro de Backup</h1>
    </div>

    <form action="{{ route('backup.backups.update', $backup->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-6">

            <div>
                <label class="block font-semibold text-gray-700 mb-1">Nome do Arquivo (Exibição)</label>
                <input type="text"
                       name="file_name"
                       value="{{ old('file_name', $backup->file_name) }}"
                       class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500 outline-none @error('file_name') border-red-500 @enderror">
                <p class="text-xs text-gray-500 mt-1 italic text-amber-600">
                    <i class="fas fa-exclamation-circle"></i> Alterar o nome aqui mudará apenas como ele aparece na lista, não o nome físico no disco.
                </p>
                @error('file_name')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4 bg-gray-50 p-4 rounded border border-gray-200">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Tamanho Atual</label>
                    <p class="text-lg font-bold text-gray-700">{{ $backup->size }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Data de Criação</label>
                    <p class="text-lg font-bold text-gray-700">{{ $backup->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div class="mt-2">
                    <label class="block text-sm font-medium text-gray-500">Caminho no Servidor</label>
                    <code class="text-xs bg-gray-200 px-1 rounded">{{ $backup->file_path }}</code>
                </div>
                <div class="mt-2 text-right">
                    <label class="block text-sm font-medium text-gray-500">Responsável</label>
                    <p class="text-sm font-semibold text-gray-700">{{ $backup->user->name ?? 'Sistema' }}</p>
                </div>
            </div>

            <div>
                <label class="block font-semibold text-gray-700 mb-1">Status do Registro</label>
                <select name="status" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="success" {{ old('status', $backup->status) == 'success' ? 'selected' : '' }}>Sucesso (Arquivo íntegro)</option>
                    <option value="failed" {{ old('status', $backup->status) == 'failed' ? 'selected' : '' }}>Falha (Problema no dump)</option>
                    <option value="archived" {{ old('status', $backup->status) == 'archived' ? 'selected' : '' }}>Arquivado (Protegido contra limpeza)</option>
                </select>
            </div>

            <hr class="my-2 border-gray-100">

            <div class="flex gap-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-2 rounded shadow transition font-bold text-lg flex items-center gap-2">
                    <i class="fas fa-save"></i> Atualizar Registro
                </button>
                <a href="{{ route('backup.backups.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded transition flex items-center gap-2 font-semibold">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </div>
    </form>
</div>

</body>
</html>
