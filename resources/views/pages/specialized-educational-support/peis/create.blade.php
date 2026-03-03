@extends('layouts.master')

@section('title', 'Gerar PEI')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            $student->person->name => route('specialized-educational-support.students.show', $student),
            'PEIs' => route('specialized-educational-support.pei.index', $student),
            'Gerar Novo' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Gerar Novo PEI</h2>
            <p class="text-muted">
                Revise as informações abaixo. O sistema criará automaticamente o PEI com base nesses dados.
            </p>
        </div>

        <x-buttons.link-button 
            href="{{ route('specialized-educational-support.pei.index', $student) }}" 
            variant="secondary">
            <i class="fas fa-times"></i> Cancelar
        </x-buttons.link-button>
    </div>

    <div class="mt-3">
        <x-forms.form-card 
            action="{{ route('specialized-educational-support.pei.store', $student) }}" 
            method="POST"
        >
            @csrf

            <x-forms.section title="Informações do PEI" />

            <x-show.info-item 
                label="Estudante"
                column="col-md-6"
                isBox="true"
            >
                <strong>{{ $student->person->name }}</strong>
            </x-show.info-item>

            <x-show.info-item 
                label="Curso Vinculado"
                column="col-md-6"
                isBox="true"
            >
                <strong>{{ $course->name }}</strong>
            </x-show.info-item>

            <x-show.info-item 
                label="Semestre Vigente"
                column="col-md-6"
                isBox="true"
            >
                <strong>{{ $semester->label }}</strong>
            </x-show.info-item>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4 mt-4">
                <x-buttons.link-button 
                    href="{{ route('specialized-educational-support.pei.index', $student) }}" 
                    variant="secondary">
                    <i class="fas fa-times"></i> Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button 
                    type="submit" 
                    class="btn-action new submit">
                    <i class="fas fa-check"></i> Confirmar e Gerar PEI
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
@endsection