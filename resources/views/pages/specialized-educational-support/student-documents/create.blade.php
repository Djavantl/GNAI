@extends('layouts.master')

@section('title', 'Cadastrar Documento')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            'Documentos' => route('specialized-educational-support.student-documents.index', $student),
            'Cadastrar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Novo Documento para {{ $student->person->name }}</h2>
            <p class="text-muted">Faça o upload de laudos, avaliações ou planos de AEE para este estudante.</p>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.student-documents.store', $student) }}" method="POST" enctype="multipart/form-data">
            
            <x-forms.section title="Informações do Documento" />

            {{-- Input oculto para garantir o student_id se necessário, 
                 embora a rota já o carregue --}}
            <input type="hidden" name="student_id" value="{{ $student->id }}">

            <div class="col-md-12 mb-3">
                <x-forms.input 
                    name="title" 
                    label="Título do Documento *" 
                    placeholder="Ex: Laudo Médico Psicológico 2024"
                    required 
                    :value="old('title')" 
                />
            </div>

            <div class="col-md-6 mb-3">
                <x-forms.select
                    name="type"
                    label="Tipo de Documento *"
                    required
                    :options="$types"  {{-- Variável limpa vinda do Controller --}}
                    :value="old('type', $document->type->value ?? '')"
                />
            </div>

            <x-show.info-item
                label="Semestre Atual"
                :value="$semester->label ?? 'Definido automaticamente'"
                column="col-md-4"
                isBox="true"
            />

            <x-forms.section title="Arquivo" />

            <div class="col-md-12 mb-4">
                <label class="form-label fw-bold">Selecionar Arquivo *</label>
                <input type="file" name="file" class="form-control" required accept=".pdf,.doc,.docx,.jpg,.png">
                <small class="text-muted">Formatos aceitos: PDF, DOC, DOCX, JPG ou PNG. Tamanho máximo: 10MB.</small>
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('specialized-educational-support.student-documents.index', $student) }}" variant="secondary">
                    Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit px-5">
                    <i class="fas fa-upload mr-2"></i> Realizar Upload
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
@endsection