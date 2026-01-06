<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Deficiência</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-4">

    <h2 class="mb-3">Editar Deficiência</h2>

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

    <form action="{{ route('deficiencies.update', $deficiency) }}" method="POST" class="card p-3">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text"
                   name="name"
                   value="{{ old('name', $deficiency->name) }}"
                   class="form-control"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">Código</label>
            <input type="text"
                   name="code"
                   value="{{ old('code', $deficiency->code) }}"
                   class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">CID</label>
            <input type="text"
                   name="cid_code"
                   value="{{ old('cid_code', $deficiency->cid_code) }}"
                   class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Descrição</label>
            <textarea name="description"
                      class="form-control"
                      rows="3">{{ old('description', $deficiency->description) }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="is_active" class="form-select">
                <option value="1" {{ old('is_active', $deficiency->is_active) ? 'selected' : '' }}>Ativa</option>
                <option value="0" {{ !old('is_active', $deficiency->is_active) ? 'selected' : '' }}>Inativa</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">
            Salvar Alterações
        </button>
    </form>

</div>

</body>
</html>
