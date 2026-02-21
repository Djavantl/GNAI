<nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" onclick="toggleSidebar()">
            <span class="navbar-toggler-icon"></span>
        </button>

        <a class="navbar-brand me-auto" href="#">
            <i class="bi bi-layers-half me-2"></i>
            <span class="fw-bold">GNAI</span>
        </a>

        <div class="d-flex align-items-center">
            
            {{-- Importando Notificações --}}
            @include('partials._notifications')

            {{-- Importando Menu do Usuário --}}
            @include('partials._user_menu')

        </div>
    </div>
</nav>