<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Aluno</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
    <h2 class="mb-3">Cadastrar Novo Aluno</h2>

    <a href="{{ route('specialized-educational-support.students.index') }}" class="btn btn-secondary mb-3">
        Voltar
    </a>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Corrija os erros abaixo:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('specialized-educational-support.students.store') }}" method="POST" class="card p-4">
        @csrf

        {{-- ================= DADOS PESSOAIS ================= --}}
        <h5 class="mb-3">Dados Pessoais</h5>

        <div class="mb-3">
            <label class="form-label">Nome Completo</label>
            <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Documento</label>
            <input type="text" name="document" value="{{ old('document') }}" class="form-control" required>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Data de Nascimento</label>
                <input type="date" name="birth_date" value="{{ old('birth_date') }}" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Gênero</label>
                <select name="gender" class="form-select">
                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Masculino</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Feminino</option>
                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Outro</option>
                    <option value="not_specified" {{ old('gender', 'not_specified') == 'not_specified' ? 'selected' : '' }}>
                        Não informado
                    </option>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">E-mail</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Telefone</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="form-control">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Endereço</label>
            <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
        </div>

        <hr>

        {{-- ================= DADOS ACADÊMICOS ================= --}}
        <h5 class="mb-3">Dados Acadêmicos</h5>

        <div class="mb-3">
            <label class="form-label">Matrícula</label>
            <input type="text" name="registration" value="{{ old('registration') }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Data de Ingresso</label>
            <input type="date" name="entry_date" value="{{ old('entry_date') }}" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success">
            Salvar
        </button>
    </form>
</div>

</body>
</html>
