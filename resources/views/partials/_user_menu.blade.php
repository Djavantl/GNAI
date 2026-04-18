<div class="dropdown">
    <button class="btn btn-user-profile dropdown-toggle shadow-none"
            type="button"
            data-bs-toggle="dropdown"
            aria-expanded="false"
            id="userMenuButton"
            aria-label="Menu do usuário: {{ Auth::user()->name }}">

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
             class="user-avatar-img"
             width="35"
             height="35"
             loading="lazy"
        >
    </button>

    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2" aria-labelledby="userMenuButton" style="border-radius: 4px; min-width: 200px;">
        <li>
            <a class="dropdown-item py-2" href="{{ route('profile.edit') }}">
                <i class="bi bi-person me-2 text-muted"></i> Perfil
            </a>
        </li>
        @if(session()->has('impersonator_id'))
        <li><hr class="dropdown-divider"></li>
        <li>
            <form method="POST" action="{{ route('admin.impersonate.leave') }}">
                @csrf
                <button class="dropdown-item text-warning py-2">
                    <i class="bi bi-arrow-counterclockwise me-2"></i>
                    Voltar para Admin
                </button>
            </form>
        </li>
        @endif
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
