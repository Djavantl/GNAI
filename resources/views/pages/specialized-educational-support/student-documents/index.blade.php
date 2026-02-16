@extends('layouts.master')

@section('title', 'Documentos do Aluno')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            'Documentos' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title mb-0">Documentos de {{ $student->person->name }}</h2>
            <p class="text-muted">Gestão de laudos, relatórios e planos de AEE</p>
        </div>
        
        <x-buttons.link-button
            :href="route('specialized-educational-support.student-documents.create', $student)"
            variant="new"
        >
             Adicionar Documento
        </x-buttons.link-button>
    </div>

    <x-table.table :headers="['Título', 'Tipo', 'Semestre', 'Versão', 'Tamanho', 'Data de Upload', 'Ações']">
        @foreach($documents as $document)
            <tr>
                <x-table.td>
                    <span class="fw-bold text-purple-dark">{{ $document->title }}</span>
                    <br>
                    <small class="text-muted">{{ $document->original_name }}</small>
                </x-table.td>
                
                <x-table.td>   
                        {{ $document->type->label() }}
                </x-table.td>

                <x-table.td>{{ $document->semester->label }}</x-table.td>

                <x-table.td>
                    v{{ $document->version }}
                </x-table.td>

                <x-table.td>
                    {{ number_format($document->file_size / 1024 / 1024, 2) }} MB
                </x-table.td>

                <x-table.td>
                    {{ $document->created_at->format('d/m/Y H:i') }}
                </x-table.td>

                <x-table.td>
                    <x-table.actions>
                        {{-- 1. BOTÃO VER (Visualização no Navegador ou Google Docs) --}}
                        @php
                            $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);
                            $isViewable = in_array(strtolower($extension), ['pdf', 'jpg', 'jpeg', 'png']);
                            
                            // Se for PDF/Imagem, abre direto. Se for DOCX, usa o visualizador do Google.
                            $viewUrl = $isViewable 
                                ? Storage::disk('local')->url($document->file_path) 
                                : "https://docs.google.com/gview?url=" . Storage::disk('public')->url($document->file_path) . "&embedded=true";
                        @endphp

                        <x-buttons.link-button
                            :href="route('specialized-educational-support.student-documents.view', $document)" {{-- Rota interna segura --}}
                            target="_blank"
                            variant="info"
                        >
                            <i class="fas fa-eye"></i> Ver
                        </x-buttons.link-button>

                        {{-- 2. BOTÃO BAIXAR (Download forçado) --}}
                        <x-buttons.link-button
                            :href="route('specialized-educational-support.student-documents.download', $document)"
                            variant="secondary"
                            title="Baixar Arquivo"
                        >
                            <i class="fas fa-download"></i>
                        </x-buttons.link-button>

                        {{-- 3. BOTÃO EDITAR --}}
                        <x-buttons.link-button
                            :href="route('specialized-educational-support.student-documents.edit', $document)"
                            variant="warning"
                        >
                            <i class="fas fa-edit"></i>
                        </x-buttons.link-button>

                        {{-- 4. BOTÃO EXCLUIR --}}
                        <form action="{{ route('specialized-educational-support.student-documents.destroy', $document) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <x-buttons.submit-button
                                variant="danger"
                                onclick="return confirm('Deseja excluir este documento permanentemente?')"
                            >
                                <i class="fas fa-trash"></i>
                            </x-buttons.submit-button>
                        </form>
                    </x-table.actions>
                </x-table.td>
            </tr>
        @endforeach

        @if($documents->isEmpty())
            <tr>
                <td colspan="7" class="text-center py-4 text-muted">
                    Nenhum documento encontrado para este aluno.
                </td>
            </tr>
        @endif
    </x-table.table>
@endsection