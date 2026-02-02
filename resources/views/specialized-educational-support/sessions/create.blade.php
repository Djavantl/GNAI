<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Criar Sessão</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
    <div class="container" style="max-width: 800px;">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Agendar Nova Sessão</h4>
            </div>
            <div class="card-body">
                <pre>
                    Total de alunos: {{ count($students) }}
                    Total de profissionais: {{ count($professionals) }}
                </pre>
                <form action="{{ route('specialized-educational-support.sessions.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Aluno</label>
                            <select name="student_id" class="form-select @error('student_id') is-invalid @enderror">
                                <option value="">Selecione...</option>
                                @foreach($students as $student)
                                   <option value="{{ $student->id }}">{{ $student->person->name ?? 'Sem Nome' }}</option>
                                @endforeach
                            </select>
                            @error('student_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Profissional</label>
                            <select name="professional_id" class="form-select @error('professional_id') is-invalid @enderror">
                                <option value="">Selecione...</option>
                                @foreach($professionals as $prof)
                                    <option value="{{ $prof->id }}">{{ $prof->person->name ?? 'Sem Nome'  }}</option>
                                @endforeach
                            </select>
                            @error('professional_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Data</label>
                            <input type="date" name="session_date" class="form-control" value="{{ old('session_date') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Início</label>
                            <input type="time" name="start_time" class="form-control" value="{{ old('start_time') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fim</label>
                            <input type="time" name="end_time" class="form-control" value="{{ old('end_time') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tipo</label>
                            <input type="text" name="type" class="form-control" placeholder="Ex: Individual" value="{{ old('type') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Local</label>
                            <input type="text" name="location" class="form-control" value="{{ old('location') }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Objetivo da Sessão</label>
                            <textarea name="session_objective" class="form-control" rows="3">{{ old('session_objective') }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="Agendado">Agendado</option>
                                <option value="Realizado">Realizado</option>
                                <option value="Cancelado">Cancelado</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-success">Salvar Sessão</button>
                        <a href="{{ route('specialized-educational-support.sessions.index') }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>