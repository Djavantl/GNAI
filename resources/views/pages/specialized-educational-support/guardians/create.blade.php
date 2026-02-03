<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Responsável</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">

    <h2 class="mb-3">Cadastrar Responsável</h2>

    <a href="{{ route('specialized-educational-support.guardians.index', $student) }}" class="btn btn-secondary mb-3">
        Voltar
    </a>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Corrija os erros:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('specialized-educational-support.guardians.store', $student) }}" method="POST" class="card p-4">
        @csrf

        <h5 class="mb-3">Dados do Responsável</h5>

        <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Documento</label>
            <input type="text" name="document" value="{{ old('document') }}" class="form-control" required>
        </div>

        <div class="row mb-3">
            <div class="col">
                <label class="form-label">Data de Nascimento</label>
                <input type="date" name="birth_date" value="{{ old('birth_date') }}" class="form-control" required>
            </div>
            <div class="col">
                <label class="form-label">Gênero</label>
                <select name="gender" class="form-select">
                    <option value="male">Masculino</option>
                    <option value="female">Feminino</option>
                    <option value="other">Outro</option>
                    <option value="not_specified" selected>Não informado</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">E-mail</label>
            <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Telefone</label>
            <input type="text" name="phone" value="{{ old('phone') }}" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Endereço</label>
            <textarea name="address" class="form-control">{{ old('address') }}</textarea>
        </div>

        <hr>

        <div class="mb-3">
            <label class="form-label">Parentesco</label>
            <select name="relationship" class="form-select" required>
                <option value="">Selecione</option>
                <option value="Pai">Pai</option>
                <option value="Mãe">Mãe</option>
                <option value="Avô">Avô</option>
                <option value="Avó">Avó</option>
                <option value="Responsável Legal">Responsável Legal</option>
                <option value="Outro">Outro</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">
            Salvar
        </button>
    </form>

</div>

</body>
</html>
