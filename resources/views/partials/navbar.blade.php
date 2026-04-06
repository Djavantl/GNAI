<nav class="navbar navbar-expand-lg navbar-dark navbar-custom" role="navigation" aria-label="Navegação Principal">
    <div class="container-fluid">

        <div class="d-flex align-items-center">

            <button id="sidebarToggle"
                    class="btn btn-fw text-white p-0 me-3"
                    type="button"
                    aria-label="Abrir ou fechar menu lateral">
                <i class="bi bi-list fs-3" aria-hidden="true"></i>
            </button>

            @if(session()->has('impersonator_id'))
            <div class="alert alert-warning py-1 px-3 mb-0 me-3 d-flex align-items-center">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <small>
                    Você está navegando como <strong>{{ auth()->user()->name }}</strong>
                </small>
            </div>
            @endif

            <a class="navbar-brand me-auto d-flex align-items-center"
               href="{{ route('inclusive-radar.institutions.index') }}"
               aria-label="Gerenciar Instituição: {{ $institution?->name ?? 'GNAI' }}">

                <span class="fw-bold">
                    {{ $institution?->name ?? 'GNAI' }}
                </span>
            </a>
        </div>

        <div class="d-flex align-items-center gap-2">
            <div role="region" aria-label="Notificações">
                @include('partials._notifications')
            </div>

            <div role="region" aria-label="Menu do usuário">
                @include('partials._user_menu')
            </div>
        </div>

    </div>
</nav>