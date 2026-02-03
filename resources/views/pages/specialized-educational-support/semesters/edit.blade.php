<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Semestre</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-8">
<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">

    <h1 class="text-2xl font-bold mb-6">
        Editar Semestre: {{ $semester->label ?? $semester->year . '.' . $semester->term }}
    </h1>

    <form action="{{ route('specialized-educational-support.semesters.update', $semester) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-4">

            <div>
                <label class="block font-semibold">Ano</label>
                <input type="number"
                       name="year"
                       value="{{ old('year', $semester->year) }}"
                       class="w-full border p-2 rounded">
            </div>

            <div>
                <label class="block font-semibold">Período</label>
                <input type="number"
                       name="term"
                       value="{{ old('term', $semester->term) }}"
                       class="w-full border p-2 rounded">
            </div>

            <div>
                <label class="block font-semibold">Rótulo</label>
                <input type="text"
                       name="label"
                       value="{{ old('label', $semester->label) }}"
                       class="w-full border p-2 rounded">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold">Início</label>
                    <input type="date"
                           name="start_date"
                           value="{{ old('start_date', $semester->start_date) }}"
                           class="w-full border p-2 rounded">
                </div>

                <div>
                    <label class="block font-semibold">Fim</label>
                    <input type="date"
                           name="end_date"
                           value="{{ old('end_date', $semester->end_date) }}"
                           class="w-full border p-2 rounded">
                </div>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox"
                       name="is_current"
                       value="1"
                       {{ old('is_current', $semester->is_current) ? 'checked' : '' }}>
                <label>Semestre atual</label>
            </div>

            <div class="flex gap-4 mt-4">
                <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    Atualizar
                </button>

                <a href="{{ route('specialized-educational-support.semesters.index') }}"
                   class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">
                    Cancelar
                </a>
            </div>

        </div>
    </form>

</div>
</body>
</html>
