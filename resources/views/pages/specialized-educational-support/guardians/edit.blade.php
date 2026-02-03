<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Responsável</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">

    <h2 class="mb-3">Editar Responsável</h2>

    <a href="{{ route('specialized-educational-support.guardians.index', $student) }}" class="btn btn-secondary mb-3">
        Voltar
    </a>

    <form action="{{ route('specialized-educational-support.guardians.update', [$student, $guardian]) }}" method="POST" class="card p-4">
        @csrf
        @method('PUT')

        <h5 class="mb-3">Dados do Responsável</h5>

        <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text"
                   name="name"
                   value="{{ old('name', $guardian->person->name) }}"
                   class="form-control"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">Documento</label>
            <input type="text"
                   name="document"
                   value="{{ old('document', $guardian->person->document) }}"
                   class="form-control"
                   required>
        </div>

        <div class="row mb-3">
            <div class="col">
                <label class="form-label">Data de Nascimento</label>
                <input type="date"
                       name="birth_date"
                       value="{{ old('birth_date', $guardian->person->birth_date->format('Y-m-d')) }}"
                       class="form-control"
                       required>
            </div>
            <div class="col">
                <label class="form-label">Gênero</label>
                <select name="gender" class="form-select">
                    @foreach(\App\Models\Person::genderOptions() as $key => $label)
                        <option value="{{ $key }}"
                            {{ old('gender', $guardian->person->gender) === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">E-mail</label>
            <input type="email"
                   name="email"
                   value="{{ old('email', $guardian->person->email) }}"
                   class="form-control"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">Telefone</label>
            <input type="text"
                   name="phone"
                   value="{{ old('phone', $guardian->person->phone) }}"
                   class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Endereço</label>
            <textarea name="address" class="form-control">{{ old('address', $guardian->person->address) }}</textarea>
        </div>

        <hr>

        <div class="mb-3">
            <label class="form-label">Parentesco</label>
            <select name="relationship" class="form-select" required>
                <option value="father" {{ $guardian->relationship === 'father' ? 'selected' : '' }}>Pai</option>
                <option value="mother" {{ $guardian->relationship === 'mother' ? 'selected' : '' }}>Mãe</option>
                <option value="guardian" {{ $guardian->relationship === 'guardian' ? 'selected' : '' }}>Responsável Legal</option>
                <option value="other" {{ $guardian->relationship === 'other' ? 'selected' : '' }}>Outro</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">
            Atualizar
        </button>
    </form>

</div>

</body>
</html>
