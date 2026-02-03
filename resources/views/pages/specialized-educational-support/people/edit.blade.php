<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Pessoa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-6">Editar: {{ $person->name }}</h1>

    <form action="{{ route('specialized-educational-support.people.update', $person) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-4">
            <div>
                <label class="block font-semibold">Nome Completo</label>
                <input type="text" name="name" value="{{ old('name', $person->name) }}"
                       class="w-full border p-2 rounded">
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block font-semibold">Documento (CPF)</label>
                <input type="text" name="document" value="{{ old('document', $person->document) }}"
                       class="w-full border p-2 rounded">
                @error('document') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold">Data de Nascimento</label>
                    <input type="date" name="birth_date"
                           value="{{ old('birth_date', $person->birth_date?->format('Y-m-d')) }}"
                           class="w-full border p-2 rounded">
                </div>
                <div>
                    <label class="block font-semibold">Gênero</label>
                    <select name="gender" class="w-full border p-2 rounded">
                        @foreach(\App\Models\SpecializedEducationalSupport\Person::genderOptions() as $key => $label)
                            <option value="{{ $key }}" {{ old('gender', $person->gender) == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-semibold">E-mail</label>
                <input type="email" name="email" value="{{ old('email', $person->email) }}"
                       class="w-full border p-2 rounded">
            </div>

            <div>
                <label class="block font-semibold">Telefone</label>
                <input type="text" name="phone" value="{{ old('phone', $person->phone) }}"
                       class="w-full border p-2 rounded">
            </div>

            <div class="flex gap-4 mt-4">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Atualizar
                </button>
                <a href="{{ route('specialized-educational-support.people.index') }}"
                   class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">Cancelar</a>
            </div>
        </div>
    </form>
</div>
</body>
</html>
