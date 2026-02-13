@extends('layouts.master')

@section('title', 'Relatórios')

@section('content')

    <div class="mb-5">
        <x-breadcrumb :items="[
        'Home' => route('dashboard'),
        'Relatórios' => route('reports.index')
    ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Relatórios</h2>
            <p class="text-muted text-base">
                Gere relatórios personalizados selecionando os filtros desejados.
            </p>
        </div>
    </div>
