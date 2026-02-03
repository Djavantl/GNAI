<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cargos — Lista</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-4">

    <h2 class="mb-3">Lista de Cargos</h2>

    <a href="{{ route('specialized-educational-support.positions.create') }}" class="btn btn-primary mb-3">
        + Novo Cargo
    </a>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Ativa?</th>
                <th style="width: 200px;">Ações</th>
            </tr>
        </thead>

        <tbody>
        @foreach($position as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->description ?? '-' }}</td>

                <td>
                    @if($item->is_active)
                        <span class="badge bg-success">Sim</span>
                    @else
                        <span class="badge bg-danger">Não</span>
                    @endif
                </td>

                <td class="d-flex gap-1">

                    <a href="{{ route('specialized-educational-support.positions.edit', $item) }}"
                       class="btn btn-sm btn-warning">
                        Editar
                    </a>

                    <form action="{{ route('specialized-educational-support.positions.deactivate', $item) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button class="btn btn-sm btn-outline-secondary">
                            Ativar / Desativar
                        </button>
                    </form>

                    <form action="{{ route('specialized-educational-support.positions.destroy', $item) }}" method="POST"
                          onsubmit="return confirm('Tem certeza que deseja excluir?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">
                            Excluir
                        </button>
                    </form>

                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

</div>

</body>
</html>
