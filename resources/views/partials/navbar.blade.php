<nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
    <div class="container-fluid">

        <a class="navbar-brand me-auto" href="#">
            <button id="sidebarToggle" class="btn btn-fw text-white p-0 me-3" type="button">
                <i class="bi bi-list fs-3"></i>
            </button>
            @php
                $institution = \App\Models\InclusiveRadar\Institution::first();
            @endphp
            <span class="fw-bold">
                @if($institution)
                    {{$institution->name}}
                @endif
            </span>
        </a>

        <div class="d-flex align-items-center">
            
            {{-- Importando Notificações --}}
            @include('partials._notifications')

            {{-- Importando Menu do Usuário --}}
            @include('partials._user_menu')

        </div>
    </div>
</nav>