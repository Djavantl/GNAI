@extends('layouts.master')

@section('title', 'Profissionais')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <h2 class="text-title">Profissionais</h2>
        <x-buttons.link-button 
            :href="route('specialized-educational-support.professionals.create')"
            variant="new"
        >
             Novo Profissional
        </x-buttons.link-button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <x-table.table :headers="['Nome', 'Documento', 'Cargo', 'Status', 'Ações']">
    @foreach($professionals as $professional)
        <tr>
            <x-table.td>{{ $professional->person->name }}</x-table.td>
            <x-table.td>{{ $professional->person->document }}</x-table.td>
            <x-table.td>{{ $professional->position->name }}</x-table.td>
            <x-table.td>
                @php
                    $statusColor = $professional->status === 'active' ? 'success' : 'danger';
                    $statusLabel = $professional->status === 'active' ? 'Ativo' : 'Inativo';
                @endphp
                
                <span class="text-{{ $statusColor }} fw-bold">
                    {{ $statusLabel }}
                </span>
            </x-table.td>

            <x-table.td>
                <x-table.actions>
                    <x-buttons.link-button 
                        :href="route('specialized-educational-support.professionals.show', $professional)"
                        variant="info"
                    >
                        ver
                    </x-buttons.link-button>

                    <x-buttons.link-button 
                        :href="route('specialized-educational-support.professionals.edit', $professional)"
                        variant="warning"
                    >
                        Editar
                    </x-buttons.link-button>

                    <form action="{{ route('specialized-educational-support.professionals.destroy', $professional) }}"
                        method="POST">
                        @csrf
                        @method('DELETE')

                        <x-buttons.submit-button 
                            variant="danger"
                            onclick="return confirm('Deseja remover este profissional?')"
                        >
                            Excluir
                        </x-buttons.submit-button>
                    </form>
                </x-table.actions>
            </x-table.td>
        </tr>
    @endforeach
    </x-table.table>
@endsection