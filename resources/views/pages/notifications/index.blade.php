@extends('layouts.app')

@section('content')

    {{-- Breadcrumb --}}
    <div class="mb-4">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Notificações' => null
        ]" />
    </div>

    {{-- Cabeçalho --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="text-title">Notificações</h2>
            <p class="text-muted">
                Histórico completo das suas notificações do sistema.
            </p>
        </div>

        <form action="{{ route('notifications.readAll') }}" method="POST" class="ms-md-auto">
            @csrf
            <x-buttons.submit-button variant="primary">
                <i class="fas fa-check"></i>
                Marcar todas como lidas
            </x-buttons.submit-button>
        </form>
    </div>

    {{-- Lista de notificações --}}

    @forelse($notifications as $notification)

        @php
            $data = $notification->data;
            $isUnread = is_null($notification->read_at);
            $destination = \App\Domains\Notifications\UI\Presenters\NotificationDestination::toLocalUrl($data['url'] ?? null);
        @endphp

        <div class="card card-custom {{ $isUnread ? 'border-start border-4 border-primary-custom' : '' }}">
            <div class="card-body d-flex flex-column flex-sm-row justify-content-between align-items-start gap-3">

                <div class="flex-grow-1">
                    <div class="fw-bold text-primary-custom">
                        {{ $data['title'] ?? 'Notificação' }}
                    </div>

                    <div class="text-muted mt-1">
                        {{ $data['message'] ?? '' }}
                    </div>

                    <div class="small text-muted mt-2">
                        <i class="far fa-clock me-1"></i>
                        {{ $notification->created_at->diffForHumans() }}
                    </div>
                </div>

                <div class="d-flex flex-wrap flex-sm-column gap-2 align-items-start align-items-sm-end flex-shrink-0">

                    @if($destination !== null)
                        <x-buttons.link-button :href="$destination" variant="success">
                            <i class="fas fa-arrow-right"></i> Abrir
                        </x-buttons.link-button>
                    @endif

                    @if($isUnread)
                        <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                            @csrf
                            <x-buttons.submit-button type="submit" class="btn-action new submit">
                                <i class="fas fa-check"></i>
                                Marcar como lida
                            </x-buttons.submit-button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

    @empty
        <div class="text-center py-5 text-muted">
            <i class="far fa-bell fa-2x mb-3"></i>
            <div>Nenhuma notificação encontrada</div>
        </div>
    @endforelse

    {{-- Paginação --}}
    <div class="mt-4">
        <x-ui.pagination :records="$notifications" />
    </div>

@endsection
