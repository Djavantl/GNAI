<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Status de Barreira</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">

<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-6">Editar Status de Barreira</h1>

    <form
        action="{{ route('inclusive-radar.barrier-statuses.update', $barrierStatus) }}"
        method="POST"
    >
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-4">
            <div>
                <label class="block font-medium">Nome</label>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $barrierStatus->name) }}"
                    class="w-full border p-2 rounded"
                >
                @error('name')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block font-medium">Descrição</label>
                <textarea
                    name="description"
                    class="w-full border p-2 rounded"
                >{{ old('description', $barrierStatus->description) }}</textarea>
            </div>

            <div class="flex items-center gap-2">
                {{-- garante envio quando desmarcado --}}
                <input type="hidden" name="is_active" value="0">

                <input
                    type="checkbox"
                    name="is_active"
                    id="is_active"
                    value="1"
                    {{ old('is_active', $barrierStatus->is_active) ? 'checked' : '' }}
                >
                <label for="is_active" class="cursor-pointer">Ativo</label>
            </div>

            <div class="flex gap-4 mt-4">
                <button
                    type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded"
                >
                    Atualizar
                </button>

                <a
                    href="{{ route('inclusive-radar.barrier-statuses.index') }}"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded"
                >
                    Cancelar
                </a>
            </div>
        </div>
    </form>
</div>

</body>
</html>
