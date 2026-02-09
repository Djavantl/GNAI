<aside class="sidebar">
    <ul class="sidebar-menu">
        <li>
            <a href="{{ route('auth.dashboard') }}"
               class="{{ request()->routeIs('auth.dashboard') ? 'active' : '' }}">
                <span class="icon"><i class="bi bi-speedometer2"></i></span>
                <span class="text">Dashboard</span>
            </a>
        </li>

        <li>
            <a href="{{ url('/inicio') }}"
               class="{{ request()->is('inicio') ? 'active' : '' }}">
                <span class="icon"><i class="bi bi-house-door"></i></span>
                <span class="text">Início</span>
            </a>
        </li>

        <li>
            <a href="{{ url('/relatorios') }}"
               class="{{ request()->is('relatorios*') ? 'active' : '' }}">
                <span class="icon"><i class="bi bi-bar-chart"></i></span>
                <span class="text">Relatórios</span>
            </a>
        </li>

        <li class="menu-divider">Atendimento AEE</li>

        <li>
            <a href="{{ route('specialized-educational-support.students.index') }}"
               class="{{ request()->routeIs('specialized-educational-support.students.*') ? 'active' : '' }}">
                <span class="icon"><i class="bi bi-people"></i></span>
                <span class="text">Alunos</span>
            </a>
        </li>

        <li>
            <a href="{{ route('specialized-educational-support.professionals.index') }}"
               class="{{ request()->routeIs('specialized-educational-support.professionals.*') ? 'active' : '' }}">
                <span class="icon"><i class="bi bi-person-badge"></i></span>
                <span class="text">Equipe</span>
            </a>
        </li>

        <li>
            <a href="{{ url('/peis') }}"
               class="{{ request()->is('peis*') ? 'active' : '' }}">
                <span class="icon"><i class="bi bi-file-text"></i></span>
                <span class="text">PEIs</span>
            </a>
        </li>

        <li>
            <a href="{{ route('specialized-educational-support.sessions.index') }}"
               class="{{ request()->routeIs('specialized-educational-support.sessions.*') ? 'active' : '' }}">
                <span class="icon"><i class="bi bi-calendar-check"></i></span>
                <span class="text">Sessões</span>
            </a>
        </li>

        <li>
            <a href="{{ route('specialized-educational-support.pendencies.index') }}"
               class="{{ request()->is('pendencias*') ? 'active' : '' }}">
                <span class="icon"><i class="bi bi-exclamation-triangle"></i></span>
                <span class="text">Pendências</span>
            </a>
        </li>

        <li class="menu-divider">Radar Inclusivo</li>

        <li>
            <a href="{{ route('inclusive-radar.assistive-technologies.index') }}"
               class="{{ request()->routeIs('inclusive-radar.assistive-technologies.*') ? 'active' : '' }}">
                <span class="icon"><i class="bi bi-cpu"></i></span>
                <span class="text">Tecnologias Assistivas</span>
            </a>
        </li>

        <li>
            <a href="{{ route('inclusive-radar.barriers.index') }}"
               class="{{ request()->routeIs('inclusive-radar.barriers.*') ? 'active' : '' }}">
                <span class="icon"><i class="bi bi-slash-circle"></i></span>
                <span class="text">Barreiras</span>
            </a>
        </li>

        <li>
            <a href="{{ route('inclusive-radar.accessible-educational-materials.index') }}"
               class="{{ request()->routeIs('inclusive-radar.accessible-educational-materials.*') ? 'active' : '' }}">
                <span class="icon"><i class="bi bi-book"></i></span>
                <span class="text">Materiais Pedagógicos</span>
            </a>
        </li>

        <li>
            <a href="{{ route('inclusive-radar.loans.index') }}"
               class="{{ request()->routeIs('inclusive-radar.loans.*') ? 'active' : '' }}">
                <span class="icon"><i class="bi bi-arrow-left-right"></i></span>
                <span class="text">Empréstimos</span>
            </a>
        </li>

        <li>
            <a href="{{ route('inclusive-radar.institutions.index') }}"
               class="{{ request()->routeIs('inclusive-radar.institutions.*') ? 'active' : '' }}">
                <span class="icon"><i class="bi bi-building"></i></span>
                <span class="text">Instituições</span>
            </a>
        </li>

        <li>
            <a href="{{ route('inclusive-radar.locations.index') }}"
               class="{{ request()->routeIs('inclusive-radar.locations.*') ? 'active' : '' }}">
                <span class="icon"><i class="bi bi-geo-alt"></i></span>
                <span class="text">Localizações</span>
            </a>
        </li>

        <li class="menu-divider">Outros</li>

        <li>
            <a href="{{ url('/acessibilidade') }}"
               class="{{ request()->is('acessibilidade*') ? 'active' : '' }}">
                <span class="icon"><i class="bi bi-universal-access"></i></span>
                <span class="text">Acessibilidade</span>
            </a>
        </li>

        <li>
            <a href="{{ url('/sobre') }}"
               class="{{ request()->is('sobre*') ? 'active' : '' }}">
                <span class="icon"><i class="bi bi-info-circle"></i></span>
                <span class="text">Sobre</span>
            </a>
        </li>
    </ul>
</aside>
