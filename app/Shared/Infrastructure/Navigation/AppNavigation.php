<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Navigation;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

final class AppNavigation
{
    public function forRequest(Request $request): array
    {
        $user = $request->user();
        if (! $user) {
            return ['entries' => [], 'currentItemKey' => null];
        }

        $groups = $this->groups();

        $entries = [];
        $currentItemKey = null;
        foreach ($groups as $groupIndex => [$title, $items]) {
            $visible = $this->visibleItems($request, $items, $currentItemKey);

            if ($visible !== [] && $title !== null) {
                $entries[] = ['key' => 'section-'.$groupIndex, 'title' => $title];
            }
            array_push($entries, ...$visible);
        }

        return compact('entries', 'currentItemKey');
    }

    /**
     * Ícone do item da sidebar ao qual um endereço pertence (ex.: empréstimo
     * em atraso → ícone de Empréstimos). Usado nas notificações; vence o item
     * cujo endereço for o prefixo mais longo do caminho.
     */
    public function iconForUrl(?string $url): ?string
    {
        $path = is_string($url) ? (parse_url($url, PHP_URL_PATH) ?: null) : null;

        if ($path === null) {
            return null;
        }

        // Endereços antigos ainda gravados em notificações → endereço atual.
        foreach (['/inclusive-radar/institutional-events' => '/inclusive-radar/publications'] as $legacy => $current) {
            if ($path === $legacy || str_starts_with($path, $legacy.'/')) {
                $path = $current.substr($path, strlen($legacy));
            }
        }

        $best = null;
        $bestLength = 0;

        foreach ($this->leafItems() as [, , $route, $iconClass]) {
            if (! is_string($route) || ! Route::has($route)) {
                continue;
            }

            $href = rtrim((string) parse_url(route($route, absolute: false), PHP_URL_PATH), '/');

            if ($href === '' || strlen($href) <= $bestLength) {
                continue;
            }

            if ($path === $href || str_starts_with($path, $href.'/')) {
                $best = $iconClass;
                $bestLength = strlen($href);
            }
        }

        return $best;
    }

    /** @return list<array<int, mixed>> itens com link, incluindo os de submenus */
    private function leafItems(): array
    {
        $leaves = [];
        $collect = function (array $items) use (&$collect, &$leaves): void {
            foreach ($items as $item) {
                if (is_array($item[6] ?? null)) {
                    $collect($item[6]);
                } else {
                    $leaves[] = $item;
                }
            }
        };

        foreach ($this->groups() as [, $items]) {
            $collect($items);
        }

        return $leaves;
    }

    /**
     * Grupos da sidebar: [título, itens]. Cada item é [chave, rótulo, rota,
     * ícone, habilidades, padrões de rota ativa, subitens opcionais].
     *
     * @return list<array{0: ?string, 1: list<array<int, mixed>>}>
     */
    private function groups(): array
    {
        return [
            [null, [
                ['dashboard', 'Dashboard', 'dashboard', 'ti-dashboard', [], ['dashboard']],
                ['notifications', 'Notificações', 'notifications.index', 'ti-bell', [], ['notifications.*']],
                ['students', 'Alunos', 'specialized-educational-support.students.index', 'ti-user', ['student.view'], ['specialized-educational-support.students.*', 'specialized-educational-support.guardians.*', 'specialized-educational-support.student-context.*', 'specialized-educational-support.student-deficiencies.*']],
                ['professionals', 'Equipe', 'specialized-educational-support.professionals.index', 'ti-id-badge', ['professional.view'], ['specialized-educational-support.professionals.*']],
                ['teachers', 'Professores', 'specialized-educational-support.teachers.index', 'ti-blackboard', ['teacher.view'], ['specialized-educational-support.teachers.*']],
                ['pei', 'PEIs', 'specialized-educational-support.pei.all', 'ti-file', ['pei.view'], ['specialized-educational-support.pei.*']],
                ['sessions', 'Agendamentos', 'specialized-educational-support.sessions.index', 'ti-calendar', ['session.view-all', 'session.view-own'], ['specialized-educational-support.sessions.*']],
                ['aee-records', 'Atendimentos Especializados', 'specialized-educational-support.aee-records.index', 'ti-book', ['aee-record.view-own', 'aee-record.view-all'], ['specialized-educational-support.aee-records.*']],
                ['pedagogical-records', 'Atendimentos Pedagógicos', 'specialized-educational-support.pedagogical-records.index', 'ti-write', ['pedagogical-record.view-own', 'pedagogical-record.view-all'], ['specialized-educational-support.pedagogical-records.*']],
                ['pendencies', 'Pendências', 'specialized-educational-support.pendencies.index', 'ti-alert', ['pendency.view'], ['specialized-educational-support.pendencies.*']],
                ['items', 'Itens', 'inclusive-radar.items.index', 'ti-package', ['item.index'], ['inclusive-radar.items.*']],
                ['loans', 'Empréstimos', 'inclusive-radar.loans.index', 'ti-exchange-vertical', ['loan.index'], ['inclusive-radar.loans.*']],
                ['waitlists', 'Fila de Espera', 'inclusive-radar.waitlists.index', 'ti-time', ['waitlist.index'], ['inclusive-radar.waitlists.*']],
                ['barriers', 'Barreiras', 'inclusive-radar.barriers.index', 'ti-na', ['barrier.index'], ['inclusive-radar.barriers.*']],
                ['publications', 'Publicações', 'inclusive-radar.publications.index', 'ti-announcement', ['publication.index'], ['inclusive-radar.publications.*']],
                ['registrations', 'Cadastros', null, 'ti-folder', [], [], [
                    ['deficiencies', 'Perfis de Atendimento', 'specialized-educational-support.deficiencies.index', 'ti-heart', ['deficiency.view'], ['specialized-educational-support.deficiencies.*']],
                    ['positions', 'Cargos', 'specialized-educational-support.positions.index', 'ti-briefcase', ['position.view'], ['specialized-educational-support.positions.*']],
                    ['semesters', 'Semestres', 'specialized-educational-support.semesters.index', 'ti-calendar', ['semester.view'], ['specialized-educational-support.semesters.*']],
                    ['disciplines', 'Disciplinas', 'specialized-educational-support.disciplines.index', 'ti-agenda', ['discipline.view'], ['specialized-educational-support.disciplines.*']],
                    ['courses', 'Cursos', 'specialized-educational-support.courses.index', 'ti-medall', ['course.view'], ['specialized-educational-support.courses.*']],
                    ['accessibility-features', 'Recursos de Acessibilidade', 'inclusive-radar.accessibility-features.index', 'ti-wheelchair', ['accessibility-feature.index'], ['inclusive-radar.accessibility-features.*']],
                    ['tags', 'Etiquetas', 'inclusive-radar.tags.index', 'fa fa-tags', ['tag.index'], ['inclusive-radar.tags.*']],
                    ['barrier-categories', 'Categorias de Barreiras', 'inclusive-radar.barrier-categories.index', 'ti-layout-grid2', ['barrier-category.index'], ['inclusive-radar.barrier-categories.*']],
                    ['institutions', 'Instituições', 'inclusive-radar.institutions.index', 'ti-home', ['institution.index'], ['inclusive-radar.institutions.*']],
                    ['locations', 'Localizações', 'inclusive-radar.locations.index', 'ti-location-pin', ['location.index'], ['inclusive-radar.locations.*']],
                ]],
            ]],
            ['Outros', [
                ['website', 'Site', 'website.builder.edit', 'ti-world', ['website.edit'], ['website.builder.*']],
                ['reports', 'Relatórios', 'reports.index', 'ti-bar-chart', ['report.index'], ['reports.*']],
                ['backups', 'Backups', 'backup.backups.index', 'ti-cloud-down', ['backup.index'], ['backup.backups.*']],
                ['audit', 'Auditoria', 'audit.index', 'ti-shield', ['audit.index'], ['audit.*']],
                ['settings', 'Configurações', 'settings.index', 'ti-settings', ['setting.view'], ['settings.*']],
                ['about', 'Sobre o Sistema', 'about-us', 'ti-info-alt', [], ['about-us']],
            ]],
        ];
    }

    /**
     * Filtra os itens pelas permissões do usuário. Itens com subitens
     * (sétima posição) só aparecem quando algum subitem estiver visível.
     *
     * @param  list<array<int, mixed>>  $items
     * @return list<array<string, mixed>>
     */
    private function visibleItems(Request $request, array $items, ?string &$currentItemKey): array
    {
        $user = $request->user();
        $visible = [];

        foreach ($items as $item) {
            [$key, $label, $route, $iconClass, $abilities, $patterns] = $item;
            $children = $item[6] ?? null;

            if (is_array($children)) {
                $visibleChildren = $this->visibleItems($request, $children, $currentItemKey);

                if ($visibleChildren !== []) {
                    $visible[] = ['key' => $key, 'label' => $label, 'iconClass' => $iconClass, 'children' => $visibleChildren];
                }

                continue;
            }

            if ($abilities !== [] && ! $user->canAny($abilities)) {
                continue;
            }

            $route = match ($key) {
                'sessions' => $user->can('session.view-all') ? $route : 'specialized-educational-support.sessions.my-sessions',
                'aee-records' => $user->can('aee-record.view-all') ? $route : 'specialized-educational-support.aee-records.my-records',
                'pedagogical-records' => $user->can('pedagogical-record.view-all') ? $route : 'specialized-educational-support.pedagogical-records.my-records',
                default => $route,
            };

            $visible[] = ['key' => $key, 'label' => $label, 'href' => route($route, absolute: false), 'iconClass' => $iconClass];
            if ($request->routeIs(...$patterns)) {
                $currentItemKey = $key;
            }
        }

        return $visible;
    }
}
