<aside class="sidebar">
    <!-- <div class="sidebar-header d-none d-md-block">
        <h6 class="mb-0 text-uppercase">Radar Inclusivo</h6>
    </div> -->

    <ul class="sidebar-menu">
        <!-- Grupo: Dashboard -->
        <li>
            <a href="{{ url('/dashboard') }}" class="{{ request()->is('dashboard') ? 'active' : '' }}">
                <span class="icon"><i class="bi bi-speedometer2"></i></span>
                <span class="text">Dashboard</span>
            </a>
        </li>

        <!-- Grupo: Principal -->
        <li>
            <a href="{{ url('/inicio') }}">
                <span class="icon"><i class="bi bi-house-door"></i></span>
                <span class="text">Início</span>
            </a>
        </li>

        <li>
            <a href="{{ url('/relatorios') }}">
                <span class="icon"><i class="bi bi-bar-chart"></i></span>
                <span class="text">Relatórios</span>
            </a>
        </li>


        <!-- Separador -->
        <li class="mt-3">
            <small class="text-white-50 px-3">ATENDENDIMENTO AEE</small>
        </li>

        <li>
            <a href="{{ route('specialized-educational-support.students.index') }}">
                <span class="icon"><i class="bi bi-people"></i></span>
                <span class="text">Alunos</span>
            </a>
        </li>

        <li>
            <a href="{{ route('specialized-educational-support.professionals.index') }}">
                <span class="icon"><i class="bi bi-person-badge"></i></span>
                <span class="text">Equipe</span>
            </a>
        </li>

        <li>
            <a href="{{ url('/peis') }}">
                <span class="icon"><i class="bi bi-file-text"></i></span>
                <span class="text">PEIs</span>
            </a>
        </li>

        <li>
            <a href="{{ route('specialized-educational-support.sessions.index') }}">
                <span class="icon"><i class="bi bi-calendar-check"></i></span>
                <span class="text">Sessões</span>
            </a>
        </li>

        <li>
            <a href="{{ url('/pendencias') }}">
                <span class="icon"><i class="bi bi-exclamation-triangle"></i></span>
                <span class="text">Pendências</span>
            </a>
        </li>

        <!-- Separador -->
        <li class="mt-3">
            <small class="text-white-50 px-3">RADAR INCLUSIVO</small>
        </li>

        <li>
            <a href="{{ route('inclusive-radar.assistive-technologies.index') }}">
                <span class="icon"><i class="bi bi-cpu"></i></span>
                <span class="text">Tecnologias Assistivas</span>
            </a>
        </li>

        <li>
            <a href="{{ route('inclusive-radar.barriers.index') }}">
                <span class="icon"><i class="bi bi-slash-circle"></i></span>
                <span class="text">Barreiras</span>
            </a>
        </li>

        <li>
            <a href="{{ route('inclusive-radar.accessible-educational-materials.index') }}">
                <span class="icon"><i class="bi bi-book"></i></span>
                <span class="text">Materiais Pedagógicos Acessíveis</span>
            </a>
        </li>

        <li>
            <a href="{{ route('inclusive-radar.loans.index') }}">
                <span class="icon"><i class="bi bi-arrow-left-right"></i></span>
                <span class="text">Empréstimos</span>
            </a>
        </li>

        <li>
            <a href="{{ route('inclusive-radar.institutions.index') }}">
                <span class="icon"><i class="bi bi-building"></i></span>
                <span class="text">Instituições</span>
            </a>
        </li>

        <li>
            <a href="{{ route('inclusive-radar.locations.index') }}">
                <span class="icon"><i class="bi bi-geo-alt"></i></span>
                <span class="text">Localizações</span>
            </a>
        </li>

        <!-- Separador -->
        <li class="mt-3">
            <small class="text-white-50 px-3">OUTROS</small>
        </li>

        <li>
            <a href="{{ url('/acessibilidade') }}">
                <span class="icon"><i class="bi bi-universal-access"></i></span>
                <span class="text">Acessibilidade</span>
            </a>
        </li>

        <li>
            <a href="{{ url('/sobre') }}">
                <span class="icon"><i class="bi bi-info-circle"></i></span>
                <span class="text">Sobre</span>
            </a>
        </li>
    </ul>
</aside>
