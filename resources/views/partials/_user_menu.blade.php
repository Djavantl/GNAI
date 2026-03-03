
<style>
    /* Botão Principal */
    .btn-user-profile {
        display: flex;
        align-items: center;
        background: transparent;
        border: 2px solid transparent; 
        padding: 5px 12px;
        transition: all 0.2s ease-in-out;
        color: white !important; /* Força o texto branco sempre */
        border-radius: 4px;
    }

    /* Hover e estado quando o dropdown está aberto (.show) */
    .btn-user-profile:hover, 
    .btn-user-profile:focus,
    .btn-user-profile.show, 
    .btn-user-profile:active {
        border-color: rgba(255, 255, 255, 0.8) !important;
        background: rgba(255, 255, 255, 0.1) !important;
        color: white !important;
        box-shadow: none !important; /* Remove sombra azul do foco se houver */
    }

    .user-info-text {
        text-align: right;
        line-height: 1.1;
    }

    .user-name {
        font-weight: 600;
        font-size: 0.85rem;
        display: block;
        color: white; /* Garante branco no nome */
    }

    .user-role {
        font-size: 0.7rem;
        display: block;
        color: rgba(255, 255, 255, 0.7) !important; /* Cargo sempre esbranquiçado */
    }

    /* Foto Redonda */
    .user-avatar-img {
        width: 35px;
        height: 35px;
        object-fit: cover;
        border-radius: 50% !important;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }
    
    .dropdown-toggle::after {
        display: none;
    }
</style>

<div class="dropdown">
    <button class="btn btn-user-profile dropdown-toggle shadow-none" type="button"
            data-bs-toggle="dropdown" aria-expanded="false">
        
        <div class="user-info-text me-2 d-none d-sm-block">
            <span class="user-name">
                {{ Str::words(Auth::user()?->name ?? 'Convidado', 1, '') }}
            </span>
            <span class="user-role text-white-50">
                @if (Auth::user()->professional)
                    {{ Auth::user()->professional?->position?->name }}
                @elseif (Auth::user()->teacher)
                    Professor
                @else
                    Admin
                @endif
                
            </span>
        </div>

        <img src="{{ Auth::user()->photo_url }}" 
             alt="Foto de {{ Auth::user()->name }}" 
             class="user-avatar-img">
    </button>

    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2" style="border-radius: 4px; min-width: 200px;">
        <li>
            <a class="dropdown-item py-2" href="{{ route('profile.edit') }}">
                <i class="bi bi-person me-2 text-muted"></i> Perfil
            </a>
        </li>
        <li>
            <a class="dropdown-item py-2" href="#">
                <i class="bi bi-gear me-2 text-muted"></i> Configurações
            </a>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
            <a class="dropdown-item text-danger py-2" href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right me-2"></i> Sair
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </a>
        </li>
    </ul>
</div>