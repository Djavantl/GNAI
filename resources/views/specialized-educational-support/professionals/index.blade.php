<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Profissionais</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Profissionais</h2>

        <a href="{{ route('specialized-educational-support.professionals.create') }}"
           class="btn btn-primary">
            Novo Profissional
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">

            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Nome</th>
                        <th>Documento</th>
                        <th>Cargo</th>
                        <th>Status</th>
                        <th width="180">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($professionals as $professional)
                        <tr>
                            <td>{{ $professional->person->name }}</td>
                            <td>{{ $professional->person->document }}</td>
                            <td>{{ $professional->position->name }}</td>
                            <td>
                                <span class="badge {{ $professional->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($professional->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('specialized-educational-support.professionals.edit', $professional) }}"
                                   class="btn btn-sm btn-warning">
                                    Editar
                                </a>

                                <form
                                    action="{{ route('specialized-educational-support.professionals.destroy', $professional) }}"
                                    method="POST"
                                    class="d-inline"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger"
                                            onclick="return confirm('Deseja remover este profissional?')">
                                        Excluir
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>

</div>

</body>
</html>
