<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Aluno</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">

    <h2 class="mb-3">Editar Aluno</h2>

    <a href="{{ route('specialized-educational-support.students.index') }}" class="btn btn-secondary mb-3">
        Voltar
    </a>

    {{-- Exibição de erros --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Corrija os erros abaixo:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('specialized-educational-support.students.update', $student) }}" method="POST" class="card p-4">
        @csrf
        @method('PUT')

        {{-- Pessoa --}}

        <h5 class="mb-3">Dados Pessoais</h5>

        <div class="mb-3">
            <label class="form-label">Nome completo</label>
            <input type="text"
                   name="name"
                   value="{{ old('name', $student->person->name) }}"
                   class="form-control"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">Documento</label>
            <input type="text"
                   name="document"
                   value="{{ old('document', $student->person->document) }}"
                   class="form-control"
                   required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Data de nascimento</label>
                <input
                    type="date"
                    name="birth_date"
                    class="form-control"
                    value="{{ old('birth_date', optional($student->person->birth_date)->format('Y-m-d')) }}"
                >
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Gênero</label>
                <select name="gender" class="form-select">
                    @php
                        $gender = old('gender', $student->person->gender);
                    @endphp
                    <option value="not_specified" {{ $gender == 'not_specified' ? 'selected' : '' }}>Não especificado</option>
                    <option value="male" {{ $gender == 'male' ? 'selected' : '' }}>Masculino</option>
                    <option value="female" {{ $gender == 'female' ? 'selected' : '' }}>Feminino</option>
                    <option value="other" {{ $gender == 'other' ? 'selected' : '' }}>Outro</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">E-mail</label>
            <input type="email"
                   name="email"
                   value="{{ old('email', $student->person->email) }}"
                   class="form-control"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">Telefone</label>
            <input type="text"
                   name="phone"
                   value="{{ old('phone', $student->person->phone) }}"
                   class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Endereço</label>
            <textarea name="address"
                      class="form-control"
                      rows="2">{{ old('address', $student->person->address) }}</textarea>
        </div>

        <hr>

        {{-- Aluno --}}

        <h5 class="mb-3">Dados Acadêmicos</h5>

        <div class="mb-3">
            <label class="form-label">Matrícula</label>
            <input type="text"
                   name="registration"
                   value="{{ old('registration', $student->registration) }}"
                   class="form-control"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">Data de ingresso</label>
            <input type="date"
                   name="entry_date"
                   value="{{ old('entry_date', $student->entry_date) }}"
                   class="form-control"
                   required>
        </div>

        <div class="d-flex gap-3 mt-4">
            <button type="submit" class="btn btn-success">
                Atualizar
            </button>

            <a href="{{ route('specialized-educational-support.students.index') }}" class="btn btn-outline-secondary">
                Cancelar
            </a>
        </div>

    </form>
</div>

</body>
</html>
