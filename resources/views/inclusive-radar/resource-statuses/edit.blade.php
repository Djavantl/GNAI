<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Status</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-8">
<div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">

    <h1 class="text-2xl font-bold mb-6">
        Editar Status
        <span class="text-sm text-gray-500">
            ({{ $resourceStatus->code }})
        </span>
    </h1>

    <form
        action="{{ route('inclusive-radar.resource-statuses.update', $resourceStatus) }}"
        method="POST"
    >
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-4">

            {{-- Nome --}}
            <div>
                <label class="block font-medium">Nome exibido</label>
                <input type="text"
                       name="name"
                       value="{{ old('name', $resourceStatus->name) }}"
                       class="w-full border p-2 rounded">
                @error('name')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            {{-- Descrição --}}
            <div>
                <label class="block font-medium">Descrição</label>
                <textarea
                    name="description"
                    class="w-full border p-2 rounded"
                >{{ old('description', $resourceStatus->description) }}</textarea>
            </div>

            {{-- Flags de bloqueio --}}
            <div class="grid grid-cols-2 gap-4">
                <label class="flex items-center gap-2">
                    <input type="checkbox"
                           name="blocks_loan"
                           value="1"
                        @checked(old('blocks_loan', $resourceStatus->blocks_loan))>
                    Bloqueia empréstimo
                </label>

                <label class="flex items-center gap-2">
                    <input type="checkbox"
                           name="blocks_access"
                           value="1"
                        @checked(old('blocks_access', $resourceStatus->blocks_access))>
                    Bloqueia acesso
                </label>
            </div>

            {{-- Aplicabilidade --}}
            <div class="grid grid-cols-2 gap-4">
                <label class="flex items-center gap-2">
                    <input type="checkbox"
                           name="for_assistive_technology"
                           value="1"
                        @checked(old(
                            'for_assistive_technology',
                            $resourceStatus->for_assistive_technology
                        ))>
                    Tecnologia Assistiva
                </label>

                <label class="flex items-center gap-2">
                    <input type="checkbox"
                           name="for_educational_material"
                           value="1"
                        @checked(old(
                            'for_educational_material',
                            $resourceStatus->for_educational_material
                        ))>
                    Material Educacional
                </label>
            </div>

            {{-- Ativo --}}
            <div class="flex items-center gap-2">
                <input type="checkbox"
                       name="is_active"
                       value="1"
                    @checked(old('is_active', $resourceStatus->is_active))>
                <label>Status ativo</label>
            </div>

            {{-- Ações --}}
            <div class="flex gap-4 mt-6">
                <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded">
                    Salvar alterações
                </button>

                <a href="{{ route('inclusive-radar.resource-statuses.index') }}"
                   class="bg-gray-500 text-white px-6 py-2 rounded">
                    Cancelar
                </a>
            </div>

        </div>
    </form>

</div>
</body>
</html>
