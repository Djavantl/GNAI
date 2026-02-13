@extends('layouts.master')

@section('title', 'Visualizar Conteúdo do PEI')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'PEIs' => route('specialized-educational-support.pei.index', $pei->student_id),
            'Plano #' . $pei->id => route('specialized-educational-support.pei.show', $pei->id),
            'Conteúdo #' . $content_programmatic->id => null
        ]" />
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-title">Detalhes do Conteúdo Programático</h2>
            <p class="text-muted">Informações sobre o conteúdo adaptado planejado.</p>
        </div>

        <div class="d-flex gap-2">
            @if(!$pei->is_finished)
                <x-buttons.link-button 
                    :href="route('specialized-educational-support.pei.content.edit', $content_programmatic)" 
                    variant="warning">
                   <i class="fas fa-edit"></i> Editar
                </x-buttons.link-button>
            @endif

            <x-buttons.link-button 
                :href="route('specialized-educational-support.pei.show', $pei->id)" 
                variant="secondary">
                <i class="fas fa-arrow-left"></i>Voltar para o Plano
            </x-buttons.link-button>
        </div>
    </div>

    <div class="custom-table-card bg-white">
        <div class="row g-0">
            
            <x-forms.section title="Informações do Conteúdo" />

            <x-show.info-item 
                label="Título" 
                :value="$content_programmatic->title" 
                column="col-md-12" 
                isBox="true" 
            />

            <x-show.info-item 
                label="Descrição / Adaptação" 
                :value="$content_programmatic->description ?? 'Nenhuma descrição detalhada.'" 
                column="col-md-12" 
                isBox="true" 
            />

            <div class="col-md-6 px-4 py-3">
                <small class="text-muted d-block">Criado em:</small>
                <span class="fw-bold">{{ $content_programmatic->created_at->format('d/m/Y H:i') }}</span>
            </div>

            <div class="col-md-6 px-4 py-3">
                <small class="text-muted d-block">Última Atualização:</small>
                <span class="fw-bold">{{ $content_programmatic->updated_at->format('d/m/Y H:i') }}</span>
            </div>

            <div class="col-12 border-top p-4 d-flex justify-content-end">
                @if(!$pei->is_finished)
                    <form method="POST" 
                          action="{{ route('specialized-educational-support.pei.content.destroy', $content_programmatic->id) }}"
                          onsubmit="return confirm('Deseja excluir este conteúdo?')">
                        @csrf
                        @method('DELETE')
                        <x-buttons.submit-button variant="danger">
                             <i class="fas fa-trash"></i> Excluir Conteúdo
                        </x-buttons.submit-button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection