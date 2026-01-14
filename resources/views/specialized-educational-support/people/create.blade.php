<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Novo Cadastro</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-6">Cadastrar Pessoa</h1>

    <form action="{{ route('specialized-educational-support.people.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label class="block">Nome Completo</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full border p-2 rounded">
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block">Documento (CPF)</label>
                <input type="text" name="document" value="{{ old('document') }}" class="w-full border p-2 rounded">
                @error('document') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block">Data de Nascimento</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date') }}"
                           class="w-full border p-2 rounded">
                </div>
                <div>
                    <label class="block">Gênero</label>
                    <select name="gender" class="w-full border p-2 rounded">
                        @foreach(\App\Models\SpecializedEducationalSupport\Person::genderOptions() as $key => $label)
                            <option
                                value="{{ $key }}" {{ old('gender') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block">E-mail</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full border p-2 rounded">
            </div>

            <div>
                <label class="block">Telefone</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="w-full border p-2 rounded">
            </div>

            <div class="flex gap-4 mt-4">
                <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded">Salvar</button>
                <a href="{{ route('specialized-educational-support.people.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded">Cancelar</a>
            </div>
        </div>
    </form>
</div>
</body>
</html>
