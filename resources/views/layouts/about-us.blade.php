<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre o Sistema — GNAI</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    @vite('resources/css/pages/about-us.css')
</head>
<body>
    <a href="{{ route('dashboard') }}" class="access-btn" aria-label="Acessar o sistema">
        <i class="bi bi-box-arrow-in-right"></i>
        <span>Acessar o Sistema</span>
    </a>

    <section class="hero">
        <div class="hero-bg">
            <div class="hero-orb hero-orb-1"></div>
            <div class="hero-orb hero-orb-2"></div>
        </div>

        <div class="hero-content">
            <div class="hero-badge">
                <span class="hero-badge-dot"></span>
                Instituto Federal Baiano — Campus Guanambi · 2026
            </div>

            <h1 class="hero-title">
                Sistema <span>GNAI</span>
            </h1>

            <p class="hero-sub">
                Gestão Estratégica de Núcleo de Atendimento Inclusivo.
                Plataforma completa para apoio à gestão educacional inclusiva,
                acompanhamento de estudantes, PEIs, tecnologias assistivas e processos institucionais.
            </p>

            <div class="hero-stats">
                <div class="hero-stat">
                    <span class="hero-stat-num">25</span>
                    <span class="hero-stat-label">Módulos</span>
                </div>
                <div class="hero-stat">
                    <span class="hero-stat-num">2</span>
                    <span class="hero-stat-label">Sistemas</span>
                </div>
                <div class="hero-stat">
                    <span class="hero-stat-num">AEE</span>
                    <span class="hero-stat-label">Foco</span>
                </div>
            </div>
        </div>

        <div class="hero-scroll">
            <div class="hero-scroll-line"></div>
            scroll
        </div>
    </section>

    <section class="features-section">
        <div class="container">
            <div class="features-header">
                <p class="section-label">Funcionalidades</p>
                <h2 class="section-title">Tudo que você precisa<br>em um só lugar</h2>
                <p class="section-desc">
                    O GNAI reúne dois grandes sistemas integrados: o <strong>Atendimento Educacional Especializado</strong>
                    e o <strong>Radar Inclusivo</strong>, cobrindo desde o cadastro até o acompanhamento completo.
                </p>
            </div>

            <div class="feature-tabs" role="tablist">
                <button class="feature-tab active" onclick="filterFeatures('all', this)">Todos</button>
                <button class="feature-tab" onclick="filterFeatures('sistema', this)">Sistema</button>
                <button class="feature-tab" onclick="filterFeatures('aee', this)">AEE</button>
                <button class="feature-tab" onclick="filterFeatures('radar', this)">Radar Inclusivo</button>
            </div>

            <div class="features-grid" id="featuresGrid">
                <div class="feature-card" data-cat="sistema">
                    <div class="feature-icon purple"><i class="bi bi-speedometer2"></i></div>
                    <div><div class="feature-name">Dashboard</div><div class="feature-desc">Visão geral com indicadores, atalhos e informações rápidas do sistema.</div></div>
                </div>
                <div class="feature-card" data-cat="sistema">
                    <div class="feature-icon violet"><i class="bi bi-bar-chart"></i></div>
                    <div><div class="feature-name">Relatórios</div><div class="feature-desc">Geração e visualização de dados consolidados: estatísticas e acompanhamento.</div></div>
                </div>
                <div class="feature-card" data-cat="sistema">
                    <div class="feature-icon pink"><i class="bi bi-bell"></i></div>
                    <div><div class="feature-name">Notificações</div><div class="feature-desc">Avisos importantes do sistema: eventos, atualizações e alertas.</div></div>
                </div>
                <div class="feature-card" data-cat="sistema">
                    <div class="feature-icon green"><i class="bi bi-cloud-arrow-down"></i></div>
                    <div><div class="feature-name">Backups</div><div class="feature-desc">Gerenciamento de cópias de segurança para proteção dos dados.</div></div>
                </div>
                <div class="feature-card" data-cat="sistema">
                    <div class="feature-icon purple"><i class="bi bi-heart-pulse"></i></div>
                    <div><div class="feature-name">Deficiências</div><div class="feature-desc">Cadastro dos tipos de deficiência dos alunos atendidos.</div></div>
                </div>
                <div class="feature-card" data-cat="sistema">
                    <div class="feature-icon violet"><i class="bi bi-briefcase"></i></div>
                    <div><div class="feature-name">Cargos</div><div class="feature-desc">Define funções dos profissionais e suas permissões de acesso.</div></div>
                </div>
                <div class="feature-card" data-cat="sistema">
                    <div class="feature-icon green"><i class="bi bi-calendar3"></i></div>
                    <div><div class="feature-name">Semestres</div><div class="feature-desc">Organiza os períodos letivos da instituição.</div></div>
                </div>
                <div class="feature-card" data-cat="sistema">
                    <div class="feature-icon purple"><i class="bi bi-mortarboard"></i></div>
                    <div><div class="feature-name">Cursos</div><div class="feature-desc">Cadastro dos cursos oferecidos pela instituição.</div></div>
                </div>
                <div class="feature-card" data-cat="sistema">
                    <div class="feature-icon violet"><i class="bi bi-book-half"></i></div>
                    <div><div class="feature-name">Disciplinas</div><div class="feature-desc">Cadastro das disciplinas vinculadas aos cursos.</div></div>
                </div>
                <div class="feature-card" data-cat="sistema">
                    <div class="feature-icon green"><i class="bi bi-universal-access"></i></div>
                    <div><div class="feature-name">Recursos de Acessibilidade</div><div class="feature-desc">Cadastro de categorias de recursos: braille, intérprete, etc.</div></div>
                </div>
                <div class="feature-card" data-cat="sistema">
                    <div class="feature-icon pink"><i class="bi bi-grid"></i></div>
                    <div><div class="feature-name">Categorias de Barreiras</div><div class="feature-desc">Classificação das barreiras: física, comunicacional, etc.</div></div>
                </div>
                <div class="feature-card" data-cat="sistema">
                    <div class="feature-icon purple"><i class="bi bi-building-fill"></i></div>
                    <div><div class="feature-name">Instituições</div><div class="feature-desc">Cadastro da instituição base do sistema.</div></div>
                </div>
                <div class="feature-card" data-cat="sistema">
                    <div class="feature-icon green"><i class="bi bi-geo-alt"></i></div>
                    <div><div class="feature-name">Localizações</div><div class="feature-desc">Locais físicos dentro das instituições: salas, setores, etc.</div></div>
                </div>
                <div class="feature-card" data-cat="aee">
                    <div class="feature-icon purple"><i class="bi bi-people"></i></div>
                    <div><div class="feature-name">Alunos</div><div class="feature-desc">Cadastro e gestão completa dos alunos atendidos pelo AEE.</div></div>
                </div>
                <div class="feature-card" data-cat="aee">
                    <div class="feature-icon violet"><i class="bi bi-person-badge"></i></div>
                    <div><div class="feature-name">Equipe</div><div class="feature-desc">Profissionais do AEE: psicólogos, pedagogos e especialistas.</div></div>
                </div>
                <div class="feature-card" data-cat="aee">
                    <div class="feature-icon green"><i class="bi bi-mortarboard"></i></div>
                    <div><div class="feature-name">Professores</div><div class="feature-desc">Cadastro de professores vinculados aos alunos atendidos.</div></div>
                </div>
                <div class="feature-card" data-cat="aee">
                    <div class="feature-icon pink"><i class="bi bi-file-text"></i></div>
                    <div><div class="feature-name">PEIs</div><div class="feature-desc">Planos Educacionais Individualizados de cada aluno.</div></div>
                </div>
                <div class="feature-card" data-cat="aee">
                    <div class="feature-icon purple"><i class="bi bi-calendar-check"></i></div>
                    <div><div class="feature-name">Sessões</div><div class="feature-desc">Registro completo dos atendimentos realizados pela equipe.</div></div>
                </div>
                <div class="feature-card" data-cat="aee">
                    <div class="feature-icon pink"><i class="bi bi-exclamation-triangle"></i></div>
                    <div><div class="feature-name">Pendências</div><div class="feature-desc">Itens que precisam de atenção ou resolução imediata.</div></div>
                </div>
                <div class="feature-card" data-cat="radar">
                    <div class="feature-icon violet"><i class="bi bi-cpu"></i></div>
                    <div><div class="feature-name">Tecnologias Assistivas</div><div class="feature-desc">Controle de equipamentos e recursos tecnológicos para acessibilidade.</div></div>
                </div>
                <div class="feature-card" data-cat="radar">
                    <div class="feature-icon green"><i class="bi bi-book"></i></div>
                    <div><div class="feature-name">Materiais Pedagógicos</div><div class="feature-desc">Materiais adaptados para apoio ao ensino inclusivo.</div></div>
                </div>
                <div class="feature-card" data-cat="radar">
                    <div class="feature-icon pink"><i class="bi bi-slash-circle"></i></div>
                    <div><div class="feature-name">Barreiras</div><div class="feature-desc">Registro de problemas de acessibilidade encontrados.</div></div>
                </div>
                <div class="feature-card" data-cat="radar">
                    <div class="feature-icon purple"><i class="bi bi-arrow-left-right"></i></div>
                    <div><div class="feature-name">Empréstimos</div><div class="feature-desc">Controle de empréstimo e devolução de recursos assistivos.</div></div>
                </div>
                <div class="feature-card" data-cat="radar">
                    <div class="feature-icon violet"><i class="bi bi-hourglass-split"></i></div>
                    <div><div class="feature-name">Fila de Espera</div><div class="feature-desc">Gestão de pessoas aguardando recursos ou atendimento.</div></div>
                </div>
                <div class="feature-card" data-cat="radar">
                    <div class="feature-icon green"><i class="bi bi-calendar-day"></i></div>
                    <div><div class="feature-name">Agenda Institucional</div><div class="feature-desc">Eventos e atividades planejadas da instituição.</div></div>
                </div>
            </div>
        </div>
    </section>

    <section class="sistemas-section">
        <div class="container">
            <div class="sistemas-header">
                <p class="section-label">Arquitetura</p>
                <h2 class="section-title">Dois sistemas,<br>uma plataforma</h2>
                <p class="section-desc">
                    O GNAI integra o ciclo completo do atendimento inclusivo, do acompanhamento
                    individual do aluno à gestão dos recursos e acessibilidade da instituição.
                </p>
            </div>

            <div class="sistemas-grid">

                <div class="sistema-card sistema-aee">
                    <div class="sistema-card-header">
                        <div class="sistema-icon"><i class="bi bi-people-fill"></i></div>
                        <div>
                            <div class="sistema-name">Atendimento Educacional Especializado</div>
                            <div class="sistema-tagline">Foco no aluno e na equipe</div>
                        </div>
                    </div>
                    <ul class="sistema-items">
                        <li><i class="bi bi-check2"></i> Alunos e responsáveis</li>
                        <li><i class="bi bi-check2"></i> Equipe de profissionais</li>
                        <li><i class="bi bi-check2"></i> Professores vinculados</li>
                        <li><i class="bi bi-check2"></i> Planos Educacionais (PEIs)</li>
                        <li><i class="bi bi-check2"></i> Registro de sessões</li>
                        <li><i class="bi bi-check2"></i> Controle de pendências</li>
                    </ul>
                </div>

                <div class="sistemas-divisor">
                    <div class="sistemas-divisor-line"></div>
                    <div class="sistemas-divisor-badge">
                        <i class="bi bi-link-45deg"></i>
                        integrados
                    </div>
                    <div class="sistemas-divisor-line"></div>
                </div>

                <div class="sistema-card sistema-radar">
                    <div class="sistema-card-header">
                        <div class="sistema-icon"><i class="bi bi-broadcast"></i></div>
                        <div>
                            <div class="sistema-name">Radar Inclusivo</div>
                            <div class="sistema-tagline">Foco nos recursos e na instituição</div>
                        </div>
                    </div>
                    <ul class="sistema-items">
                        <li><i class="bi bi-check2"></i> Tecnologias assistivas</li>
                        <li><i class="bi bi-check2"></i> Materiais pedagógicos</li>
                        <li><i class="bi bi-check2"></i> Registro de barreiras</li>
                        <li><i class="bi bi-check2"></i> Empréstimos de recursos</li>
                        <li><i class="bi bi-check2"></i> Fila de espera</li>
                        <li><i class="bi bi-check2"></i> Agenda institucional</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="team-section">
        <div class="container">
            <p class="section-label">Equipe</p>
            <h2 class="section-title">Quem construiu o GNAI</h2>
            <p class="section-desc">
                Projeto desenvolvido por estudantes do curso de Análise e Desenvolvimento de Sistemas
                do IF Baiano — Campus Guanambi, com orientação docente.
            </p>

            <div class="team-grid">

                <div class="team-card">
                    <div class="team-avatar" id="avatar-djavan">
                        <img src="{{ asset('images/team/djavan.jpg') }}"
                             alt="Foto de Djavan Teixeira Lopes"
                             onerror="this.parentElement.innerHTML='DT'">
                    </div>
                    <span class="team-role-badge role-dev">Desenvolvimento</span>
                    <div class="team-name">Djavan Teixeira Lopes</div>
                    <a href="mailto:djavanlopesteixeira@gmail.com" class="team-email">
                        <i class="bi bi-envelope"></i> djavanlopesteixeira@gmail.com
                    </a>
                    <a href="https://github.com/Djavantl" target="_blank" class="team-email">
                        <i class="bi bi-github"></i> github.com/Djavantl
                    </a>
                </div>

                <div class="team-card">
                    <div class="team-avatar">
                        <img src="{{ asset('images/team/marley.jpg') }}"
                             alt="Foto de Marley Teixeira Meira"
                             onerror="this.parentElement.innerHTML='MT'">
                    </div>
                    <span class="team-role-badge role-dev">Desenvolvimento</span>
                    <div class="team-name">Marley Teixeira Meira</div>
                    <a href="mailto:mxrlrey@gmail.com" class="team-email">
                        <i class="bi bi-envelope"></i> mxrlrey@gmail.com
                    </a>
                    <a href="https://github.com/Mxrlrey" target="_blank" class="team-email">
                        <i class="bi bi-github"></i> github.com/Mxrlrey
                    </a>
                </div>

                <div class="team-card">
                    <div class="team-avatar">
                        <img src="{{ asset('images/team/woquiton.jpg') }}"
                             alt="Foto de Prof. Woquiton Fernandes"
                             onerror="this.parentElement.innerHTML='WF'">
                    </div>
                    <span class="team-role-badge role-orient">Orientação</span>
                    <div class="team-name">Prof. Woquiton Fernandes</div>
                    <a href="mailto:woquiton@gmail.com" class="team-email">
                        <i class="bi bi-envelope"></i> woquiton@gmail.com
                    </a>
                    <a href="https://github.com/Woquiton" target="_blank" class="team-email">
                        <i class="bi bi-github"></i> github.com/Woquiton
                    </a>
                </div>
            </div>
        </div>
    </section>

    <footer class="about-footer">
        <div class="about-footer-logo">GNAI</div>
        <div class="about-footer-inst">Instituto Federal Baiano — Campus Guanambi</div>
        <div class="about-footer-year">ADS · 2026 · Todos os direitos reservados</div>
    </footer>
    <script>
        function filterFeatures(cat, btn) {
            document.querySelectorAll('.feature-tab').forEach(t => t.classList.remove('active'));
            btn.classList.add('active');
            document.querySelectorAll('.feature-card').forEach(card => {
                card.classList.toggle('hidden', cat !== 'all' && card.dataset.cat !== cat);
            });
        }

        const obs = new IntersectionObserver(entries => {
            entries.forEach((e, i) => {
                if (e.isIntersecting) {
                    setTimeout(() => {
                        e.target.style.opacity = '1';
                        e.target.style.transform = 'translateY(0)';
                    }, (i % 6) * 60);
                    obs.unobserve(e.target);
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.feature-card, .team-card').forEach(c => {
            c.style.opacity = '0';
            c.style.transform = 'translateY(16px)';
            c.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            obs.observe(c);
        });
    </script>
</body>
</html>
