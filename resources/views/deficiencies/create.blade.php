<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Criar Deficiência</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-4">

    <h2 class="mb-3">Cadastrar Nova Deficiência</h2>

    <a href="{{ route('deficiencies.index') }}" class="btn btn-secondary mb-3">
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

    <form action="{{ route('deficiencies.store') }}" method="POST" class="card p-3">
        @csrf

        <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text"
                   name="name"
                   value="{{ old('name') }}"
                   class="form-control"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">Código</label>
            <input type="text"
                   name="code"
                   value="{{ old('code') }}"
                   class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">CID</label>
            <input type="text"
                   name="cid_code"
                   value="{{ old('cid_code') }}"
                   class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Descrição</label>
            <textarea name="description"
                      class="form-control"
                      rows="3">{{ old('description') }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="is_active" class="form-select">
                <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>Ativa</option>
                <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>Inativa</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">
            Salvar
        </button>
    </form>

</div>

</body>
</html>
