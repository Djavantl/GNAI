<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Criar Semestre</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-4">

    <h2 class="mb-3">Cadastrar Novo Semestre</h2>

    <a href="{{ route('specialized-educational-support.semesters.index') }}" class="btn btn-secondary mb-3">
        Voltar
    </a>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Corrija os erros abaixo:</strong>
            <ul class="mt-2 mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('specialized-educational-support.semesters.store') }}" method="POST" class="card p-3">
        @csrf

        <div class="mb-3">
            <label class="form-label">Ano</label>
            <input type="number"
                   name="year"
                   value="{{ old('year') }}"
                   class="form-control"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">Período (Semestre)</label>
            <input type="number"
                   name="term"
                   value="{{ old('term', 1) }}"
                   class="form-control"
                   min="1"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">Rótulo</label>
            <input type="text"
                   name="label"
                   value="{{ old('label') }}"
                   class="form-control"
                   placeholder="Ex: 2026.1">
        </div>

        <div class="mb-3">
            <label class="form-label">Data de Início</label>
            <input type="date"
                   name="start_date"
                   value="{{ old('start_date') }}"
                   class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Data de Fim</label>
            <input type="date"
                   name="end_date"
                   value="{{ old('end_date') }}"
                   class="form-control">
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox"
                   name="is_current"
                   value="1"
                   class="form-check-input"
                   {{ old('is_current') ? 'checked' : '' }}>
            <label class="form-check-label">Definir como semestre atual</label>
        </div>

        <button type="submit" class="btn btn-success">
            Salvar
        </button>
    </form>

</div>

</body>
</html>
