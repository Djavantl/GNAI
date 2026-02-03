<nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
    <div class="container-fluid">
        <!-- Botão para mobile (aparece apenas em telas pequenas) -->
        <button class="navbar-toggler" type="button" onclick="toggleSidebar()">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Logo/Marca -->
        <a class="navbar-brand mx-auto mx-lg-0" href="#">
            <i class="bi bi-layers-half me-2"></i>
            <span class="fw-bold">GNAI</span>
        </a>
        
        <!-- Itens da Navbar (vazios por enquanto) -->
        <div class="d-none d-lg-block">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <!-- Espaço para itens futuros -->
            </ul>
        </div>
        
        <!-- Menu do usuário (exemplo) -->
        <div class="dropdown">
            <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" 
                    data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle me-1"></i>
                Usuário
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i> Perfil</a></li>
                <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i> Configurações</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-box-arrow-right me-2"></i> Sair</a></li>
            </ul>
        </div>
    </div>
</nav>