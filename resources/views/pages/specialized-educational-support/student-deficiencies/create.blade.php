<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vincular Deficiência - {{ $student->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .header-bg {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="card shadow border-0">
            <div class="card-header header-bg text-white d-flex justify-content-between align-items-center p-3">
                <div>
                    <h4 class="mb-0"><i class="bi bi-link me-2"></i>Vincular Deficiência</h4>
                    <p class="mb-0 mt-2 opacity-75">{{ $student->name }} • Matrícula: {{ $student->enrollment ?? 'N/A' }}</p>
                </div>
                <a href="{{ route('specialized-educational-support.student-deficiencies.index', $student) }}" class="btn btn-light btn-sm">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('specialized-educational-support.student-deficiencies.store', $student) }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="deficiency_id" class="form-label fw-bold">Deficiência <span class="text-danger">*</span></label>
                            <select name="deficiency_id" id="deficiency_id" class="form-select @error('deficiency_id') is-invalid @enderror" required>
                                <option value="">Selecione uma deficiência...</option>
                                @foreach($deficienciesList as $deficiency)
                                    <option value="{{ $deficiency->id }}" {{ old('deficiency_id') == $deficiency->id ? 'selected' : '' }}>
                                        {{ $deficiency->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('deficiency_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="severity" class="form-label fw-bold">Severidade</label>
                            <select name="severity" id="severity" class="form-select @error('severity') is-invalid @enderror">
                                <option value="">Selecione a severidade...</option>
                                <option value="mild" {{ old('severity') == 'mild' ? 'selected' : '' }}>Leve</option>
                                <option value="moderate" {{ old('severity') == 'moderate' ? 'selected' : '' }}>Moderada</option>
                                <option value="severe" {{ old('severity') == 'severe' ? 'selected' : '' }}>Severa</option>
                            </select>
                            @error('severity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="uses_support_resources" id="uses_support_resources" value="1" {{ old('uses_support_resources') ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="uses_support_resources">
                                    Utiliza recursos de apoio?
                                </label>
                            </div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="notes" class="form-label fw-bold">Observações</label>
                            <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="4" placeholder="Observações sobre a deficiência...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('specialized-educational-support.student-deficiencies.index', $student) }}" class="btn btn-secondary px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4">Vincular Deficiência</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>