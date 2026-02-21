@extends('layouts.master')

@section('title', 'Profissionais')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Profissionais' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title mb-0">Profissionais</h2>
            <p class="text-muted">Gerencie os profissionais e seus documentos de apoio especializado.</p>
        </div>
        <x-buttons.link-button
            :href="route('specialized-educational-support.professionals.create')"
            variant="new"
        >
            <i class="fas fa-plus"></i>Adicionar
        </x-buttons.link-button>
    </div>

    <x-ui.search
        :url="route('specialized-educational-support.professionals.index')"
        target="#professionals-table"
        :semester="true"
        :semesters="$semesters"
    />


    <div id="professionals-table">
        @include('pages.specialized-educational-support.professionals.partials.table')
    </div>

    
    @push('scripts')
        @vite('resources/js/components/search-filter.js')
    @endpush
@endsection
