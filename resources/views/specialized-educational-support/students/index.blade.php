<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Alunos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
    <div class="d-flex justify-content-between mb-3">
        <h2>Alunos Cadastrados</h2>
        <a href="{{ route('specialized-educational-support.students.create') }}" class="btn btn-success">
            Novo Aluno
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-hover bg-white">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Matrícula</th>
                <th>Status</th>
                <th>Ingresso</th>
                <th>Ações</th>
            </tr>
        </thead>
            <tbody>
                @foreach($students as $student)
                    <tr>
                        <td>{{ $student->person->name }}</td>
                        <td>{{ $student->registration }}</td>
                        <td>{{ ucfirst($student->status) }}</td>
                        <td>{{ \Carbon\Carbon::parse($student->entry_date)->format('d/m/Y') }}</td>
                        <td class="d-flex gap-2">
                            <a href="{{ route('specialized-educational-support.student-context.show', $student) }}" class="btn btn-sm btn-info text-white">
                                Contexto AEE
                            </a>

                            <a href="{{ route('specialized-educational-support.students.edit', $student) }}" class="btn btn-sm btn-warning">
                                Editar
                            </a>

                            <a href="{{ route('specialized-educational-support.student-deficiencies.index', $student) }}" class="btn btn-sm btn-warning">
                                deficiencias
                            </a>
                            
                            <form action="{{ route('specialized-educational-support.students.destroy', $student) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger"
                                    onclick="return confirm('Deseja excluir este aluno?')">
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
