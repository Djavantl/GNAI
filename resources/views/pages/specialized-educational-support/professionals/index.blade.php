@extends('layouts.master')

@section('title', 'Profissionais')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Profissionais' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <h2 class="text-title">Profissionais</h2>
        <x-buttons.link-button 
            :href="route('specialized-educational-support.professionals.create')"
            variant="new"
        >
             Novo Profissional
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
