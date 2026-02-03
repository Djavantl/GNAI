<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Novo Profissional</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">

    <div class="card">
        <div class="card-header">
            <h4>Novo Profissional</h4>
        </div>

        <div class="card-body">
            <form action="{{ route('specialized-educational-support.professionals.store') }}" method="POST">
                @csrf

                <h5 class="mb-3">Dados da Pessoa</h5>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Documento</label>
                        <input type="text" name="document" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Nascimento</label>
                        <input type="date" name="birth_date" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Gênero</label>
                        <select name="gender" class="form-select">
                            <option value="not_specified">Não informado</option>
                            <option value="male">Masculino</option>
                            <option value="female">Feminino</option>
                            <option value="other">Outro</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Telefone</label>
                        <input type="text" name="phone" class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Endereço</label>
                        <input type="text" name="address" class="form-control">
                    </div>
                </div>

                <hr>

                <h5 class="mb-3">Dados do Profissional</h5>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Cargo</label>
                        <select name="position_id" class="form-select" required>
                            <option value="">Selecione</option>
                            @foreach($positions as $position)
                                <option value="{{ $position->id }}">{{ $position->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Matrícula</label>
                        <input type="text" name="registration" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Entrada</label>
                        <input type="date" name="entry_date" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active">Ativo</option>
                            <option value="inactive">Inativo</option>
                        </select>
                    </div>
                </div>

                <div class="mt-3">
                    <button class="btn btn-success">Salvar</button>
                    <a href="{{ route('specialized-educational-support.professionals.index') }}"
                       class="btn btn-secondary">
                        Voltar
                    </a>
                </div>

            </form>
        </div>
    </div>

</div>

</body>
</html>
