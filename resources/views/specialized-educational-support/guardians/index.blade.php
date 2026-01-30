<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Responsáveis do Aluno</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">

    <h2 class="mb-3">
        Responsáveis — {{ $student->person->name }}
    </h2>

    <div class="mb-3 d-flex gap-2">
        <a href="{{ route('specialized-educational-support.students.index') }}" class="btn btn-secondary">
            Voltar para alunos
        </a>

        <a href="{{ route('specialized-educational-support.guardians.create', $student) }}" class="btn btn-primary">
            Novo Responsável
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>Nome</th>
                <th>Documento</th>
                <th>Parentesco</th>
                <th>Telefone</th>
                <th width="180">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($guardians as $guardian)
                <tr>
                    <td>{{ $guardian->person->name }}</td>
                    <td>{{ $guardian->person->document }}</td>
                    <td>{{ ucfirst($guardian->relationship) }}</td>
                    <td>{{ $guardian->person->phone ?? '-' }}</td>
                    <td class="d-flex gap-2">
                        <a href="{{ route('specialized-educational-support.guardians.edit', [$student, $guardian]) }}"
                           class="btn btn-sm btn-warning">
                            Editar
                        </a>

                        <form action="{{ route('specialized-educational-support.guardians.destroy', [$student, $guardian]) }}"
                              method="POST"
                              onsubmit="return confirm('Remover responsável?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">
                                Excluir
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">
                        Nenhum responsável cadastrado.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>

</body>
</html>
