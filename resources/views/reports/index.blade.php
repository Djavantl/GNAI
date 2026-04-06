@extends('layouts.master')

@section('content')
    <h1>Relatórios</h1>

    <p>Módulo de relatórios do sistema.</p>

    <a href="{{ route('reports.builder') }}" class="btn btn-primary">
        Testar Builder
    </a>
@endsection