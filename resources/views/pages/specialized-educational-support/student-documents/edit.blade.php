@extends('layouts.master')

@section('title', 'Editar Documento')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            $student->person->name => route('specialized-educational-support.students.show', $student),
            'Documentos' => route('specialized-educational-support.student-documents.index', $studentDocument->student_id),
            $studentDocument->title => null,
            'Editar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Editar Documento</h2>
            <p class="text-muted">Atualize as informações do studentDocumento ou substitua o arquivo anexado.</p>
        </div>
        <x-buttons.link-button href="{{ route('specialized-educational-support.student-documents.index', $studentDocument->student_id) }}" variant="secondary">
            <i class="fas fa-times"></i> Cancelar
        </x-buttons.link-button>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.student-documents.update', $studentDocument) }}" method="POST" enctype="multipart/form-data">
            @method('PUT')

            <x-forms.section title="Informações Gerais" />

            <div class="col-md-12 mb-3">
                <x-forms.input 
                    name="title" 
                    label="Título do Documento " 
                    required 
                    :value="old('title', $studentDocument->title)" 
                />
            </div>

           <div class="col-md-6 mb-3">
                <x-forms.select
                    name="type"
                    label="Tipo de Documento "
                    required
                    :options="$types" 
                    :selected="$studentDocument->type->value"
                />
            </div>

            <x-show.info-item
                label="Semestre Atual"
                :value="$semester->label ?? 'Definido automaticamente'"
                column="col-md-4"
                isBox="true"
            />

            <x-forms.section title="Arquivo e Versão" />

            <div class="col-md-12 mb-4">
                <label class="form-label fw-bold">Substituir Arquivo</label>
                
                <div class="alert alert-info d-flex align-items-center mb-3">
                    <i class="fas fa-file-alt me-3 fa-2x"></i>
                    <div>
                        <strong>Arquivo Atual:</strong> {{ $studentDocument->original_name }}<br>
                        <small>Versão atual: <strong>v{{ $studentDocument->version }}</strong>. Ao enviar um novo arquivo, a versão será incrementada automaticamente.</small>
                    </div>
                </div>

                <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.jpg,.png">
                <small class="text-muted">Deixe em branco para manter o arquivo atual. Formatos: PDF, DOC, DOCX, Imagens (Máx: 10MB).</small>
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('specialized-educational-support.student-documents.index', $studentDocument->student_id) }}" variant="secondary">
                    <i class="fas fa-times"></i> Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit">
                    <i class="fas fa-save"></i> Salvar
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
@endsection