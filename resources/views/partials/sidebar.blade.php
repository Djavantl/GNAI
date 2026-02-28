<aside class="sidebar">
    <div class="sidebar-header">
        <img src="{{ asset('images/logo2.png') }}" class="sidebar-logo" alt="Logo">
        <span class="sidebar-title">NAI</span>
    </div>

    <ul class="sidebar-menu">
        
        <li>
            <a href="{{ route('dashboard') }}"
               class="{{ request()->is('auth/dashboard') ? 'active' : '' }}">
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

        @can('report.reports.index')
        <li>
            <a href="{{ route('report.reports.index') }}"
               class="{{ request()->routeIs('report.reports*') ? 'active' : '' }}">
                <span class="icon"><i class="bi bi-bar-chart"></i></span>
                <span class="text">Relatórios</span>
            </a>
        </li>
        @endcan

        <li>
            <a href="{{ route('notifications.index') }}"
                class="{{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                <span class="icon"><i class="fa fa-regular fa-bell"></i></span>
                <span class="text">Notificações</span>
            </a>
        </li>

        <li>
            <a href="{{ route('backup.backups.index') }}"
                class="{{ request()->routeIs('backup.backups.*') ? 'active' : '' }}">
                <span class="icon"><i class="fas fa-cloud-download"></i></span>
                <span class="text">Backups</span>
            </a>
        </li>

        @auth
            @if(auth()->user()->is_admin)

                <li class="menu-divider">Configurações do Sistema</li>

                {{-- ===== AEE ===== --}}
                <li>
                    <a href="{{ route('specialized-educational-support.deficiencies.index') }}"
                       class="{{ request()->routeIs('specialized-educational-support.deficiencies.*') ? 'active' : '' }}">
                        <span class="icon"><i class="bi bi-heart-pulse"></i></span>
                        <span class="text">Deficiências</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('specialized-educational-support.positions.index') }}"
                       class="{{ request()->routeIs('specialized-educational-support.positions.*') ? 'active' : '' }}">
                        <span class="icon"><i class="bi bi-briefcase"></i></span>
                        <span class="text">Cargos</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('specialized-educational-support.semesters.index') }}"
                       class="{{ request()->routeIs('specialized-educational-support.semesters.*') ? 'active' : '' }}">
                        <span class="icon"><i class="bi bi-calendar3"></i></span>
                        <span class="text">Semestres</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('specialized-educational-support.courses.index') }}"
                       class="{{ request()->routeIs('specialized-educational-support.courses.*') ? 'active' : '' }}">
                        <span class="icon"><i class="bi bi-mortarboard"></i></span>
                        <span class="text">Cursos</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('specialized-educational-support.disciplines.index') }}"
                       class="{{ request()->routeIs('specialized-educational-support.disciplines.*') ? 'active' : '' }}">
                        <span class="icon"><i class="bi bi-book-half"></i></span>
                        <span class="text">Disciplinas</span>
                    </a>
                </li>

                {{-- ===== RADAR INCLUSIVO – ADMIN ===== --}}

                <li>
                    <a href="{{ route('inclusive-radar.resource-types.index') }}"
                       class="{{ request()->routeIs('resource-types.*') ? 'active' : '' }}">
                        <span class="icon"><i class="bi bi-diagram-3"></i></span>
                        <span class="text">Tipos de Recursos</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('inclusive-radar.type-attributes.index') }}"
                       class="{{ request()->routeIs('type-attributes.*') ? 'active' : '' }}">
                        <span class="icon"><i class="bi bi-input-cursor-text"></i></span>
                        <span class="text">Atributos</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('inclusive-radar.type-attribute-assignments.index') }}"
                       class="{{ request()->routeIs('type-attribute-assignments.*') ? 'active' : '' }}">
                        <span class="icon"><i class="bi bi-link-45deg"></i></span>
                        <span class="text">Vincular Atributos</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('inclusive-radar.accessibility-features.index') }}"
                       class="{{ request()->routeIs('accessibility-features.*') ? 'active' : '' }}">
                        <span class="icon"><i class="bi bi-universal-access"></i></span>
                        <span class="text">Recursos de Acessibilidade</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('inclusive-radar.resource-statuses.index') }}"
                       class="{{ request()->routeIs('resource-statuses.*') ? 'active' : '' }}">
                        <span class="icon"><i class="bi bi-toggle-on"></i></span>
                        <span class="text">Status de Recursos</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('inclusive-radar.barrier-categories.index') }}"
                       class="{{ request()->routeIs('barrier-categories.*') ? 'active' : '' }}">
                        <span class="icon"><i class="bi bi-grid"></i></span>
                        <span class="text">Categorias de Barreiras</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('inclusive-radar.institutions.index') }}"
                       class="{{ request()->routeIs('institutions.*') ? 'active' : '' }}">
                        <span class="icon"><i class="bi bi-building-fill"></i></span>
                        <span class="text">Instituições</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('inclusive-radar.locations.index') }}"
                       class="{{ request()->routeIs('locations.*') ? 'active' : '' }}">
                        <span class="icon"><i class="bi bi-geo-alt"></i></span>
                        <span class="text">Localizações</span>
                    </a>
                </li>

            @endif
        @endauth

        <li class="menu-divider">Atendimento AEE</li>

        @can('student.view')
        <li class="nav-item">
            <a href="{{ route('specialized-educational-support.students.index') }}"
                class="{{ request()->routeIs([
                    'specialized-educational-support.students.*',
                    'specialized-educational-support.guardians.*',
                    'specialized-educational-support.student-context.*',
                    'specialized-educational-support.student-deficiencies.*'
                ]) ? 'active' : '' }}">
                <span class="icon"><i class="bi bi-people"></i></span>
                <span class="text">Alunos</span>
            </a>
        </li>
        @endcan

        @can('professional.view')
        <li>
            <a href="{{ route('specialized-educational-support.professionals.index') }}"
            class="{{ request()->routeIs('specialized-educational-support.professionals.*') ? 'active' : '' }}">
                <span class="icon"><i class="bi bi-person-badge"></i></span>
                <span class="text">Equipe</span>
            </a>
        </li>
        @endcan

        @can('teacher.view')
        <li>
            <a href="{{ route('specialized-educational-support.teachers.index') }}"
            class="{{ request()->routeIs('specialized-educational-support.teachers.*') ? 'active' : '' }}">
                <span class="icon"><i class="bi bi-mortarboard"></i></span>
                <span class="text">Professores</span>
            </a>
        </li>
        @endcan

        @can('pei.view')
        <li>
            <a href="{{ route('specialized-educational-support.pei.all') }}"
            class="{{ request()->routeIs('specialized-educational-support.pei.*') ? 'active' : '' }}">
                <span class="icon"><i class="bi bi-file-text"></i></span>
                <span class="text">PEIs</span>
            </a>
        </li>
        @endcan

        @can('session.view')
        <li>
            <a href="{{ route('specialized-educational-support.sessions.index') }}"
            class="{{ request()->routeIs('specialized-educational-support.sessions.*') ? 'active' : '' }}">
                <span class="icon"><i class="bi bi-calendar-check"></i></span>
                <span class="text">Sessões</span>
            </a>
        </li>
        @endcan

        @can('pendency.view')
        <li>
            <a href="{{ route('specialized-educational-support.pendencies.index') }}"
            class="{{ request()->routeis('specialized-educational-support.pendencies.*') ? 'active' : '' }}">
                <span class="icon"><i class="bi bi-exclamation-triangle"></i></span>
                <span class="text">Pendências</span>
            </a>
        </li>
        @endcan

        <li class="menu-divider">Radar Inclusivo</li>

        @can('assistive-technology.index')
        <li>
            <a href="{{ route('inclusive-radar.assistive-technologies.index') }}"
               class="{{ request()->routeIs('inclusive-radar.assistive-technologies.*') ? 'active' : '' }}">
                <span class="icon"><i class="bi bi-cpu"></i></span>
                <span class="text">Tecnologias Assistivas</span>
            </a>
        </li>
        @endcan

        @can('material.index')
            <li>
                <a href="{{ route('inclusive-radar.accessible-educational-materials.index') }}"
                   class="{{ request()->routeIs('inclusive-radar.accessible-educational-materials.*') ? 'active' : '' }}">
                    <span class="icon"><i class="bi bi-book"></i></span>
                    <span class="text">Materiais Pedagógicos</span>
                </a>
            </li>
        @endcan

        @can('barriers.index')
        <li>
            <a href="{{ route('inclusive-radar.barriers.index') }}"
               class="{{ request()->routeIs('inclusive-radar.barriers.*') ? 'active' : '' }}">
                <span class="icon"><i class="bi bi-slash-circle"></i></span>
                <span class="text">Barreiras</span>
            </a>
        </li>
        @endcan

        @can('loan.index')
        <li>
            <a href="{{ route('inclusive-radar.loans.index') }}"
               class="{{ request()->routeIs('inclusive-radar.loans.*') ? 'active' : '' }}">
                <span class="icon"><i class="bi bi-arrow-left-right"></i></span>
                <span class="text">Empréstimos</span>
            </a>
        </li>
        @endcan

        @can('waitlist.index')
            <li>
                <a href="{{ route('inclusive-radar.waitlists.index') }}"
                   class="{{ request()->routeIs('waitlists.*') ? 'active' : '' }}">
                    <span class="icon"><i class="bi bi-hourglass-split"></i></span>
                    <span class="text">Fila de Espera</span>
                </a>
            </li>
        @endcan

        @can('maintenance.index')
            <li>
                <a href="{{ route('inclusive-radar.maintenances.index') }}"
                   class="{{ request()->routeIs('inclusive-radar.maintenances.*') ? 'active' : '' }}">
                    <span class="icon"><i class="bi bi-tools"></i></span>
                    <span class="text">Manutenções</span>
                </a>
            </li>
        @endcan

        @can('training.index')
            <li>
                <a href="{{ route('inclusive-radar.trainings.index') }}"
                   class="{{ request()->routeIs('trainings.*') ? 'active' : '' }}">
                    <span class="icon"><i class="bi bi-mortarboard-fill"></i></span>
                    <span class="text">Treinamentos</span>
                </a>
            </li>
        @endcan

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
