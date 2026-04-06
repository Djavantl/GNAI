<?php
// app/Services/ReportService.php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ReportService
{
    protected array $config;

    public function __construct()
    {
        $this->config = config('reportables');
    }

    protected function isAllowedTable(string $table): bool
    {
        return array_key_exists($table, $this->config['tables']);
    }

    protected function isAllowedColumn(string $table, string $column): bool
    {
        return in_array($column, $this->config['tables'][$table]['columns'] ?? [], true);
    }

    // retorna relação entre duas tabelas (considera ordem)
    protected function getRelationBetween(string $a, string $b): ?array
    {
        $k1 = "{$a}.{$b}";
        $k2 = "{$b}.{$a}";
        if (isset($this->config['relations'][$k1])) return $this->config['relations'][$k1];
        if (isset($this->config['relations'][$k2])) return $this->config['relations'][$k2];
        return null;
    }

    // encontra caminho mínimo (BFS) entre base e target nas relações
    protected function findPath(string $base, string $target): ?array
    {
        $adj = [];
        foreach ($this->config['relations'] as $k => $rel) {
            [$x, $y] = explode('.', $k);
            $adj[$x][] = $y;
            $adj[$y][] = $x;
        }

        $queue = [[$base]];
        $visited = [$base => true];

        while (!empty($queue)) {
            $path = array_shift($queue);
            $last = end($path);
            if ($last === $target) return $path;

            foreach ($adj[$last] ?? [] as $nei) {
                if (!isset($visited[$nei])) {
                    $visited[$nei] = true;
                    $new = $path;
                    $new[] = $nei;
                    $queue[] = $new;
                }
            }
        }
        return null;
    }

    // cria selects com alias table__column para evitar colisões
    protected function prepareSelects(array $selects, string $base): array
    {
        if (empty($selects)) {
            return ["{$base}.*"];
        }

        $out = [];
        foreach ($selects as $s) {
            $parts = preg_split('/\s+as\s+/i', $s);
            $col = trim($parts[0]);
            $alias = $parts[1] ?? null;

            if (strpos($col, '.') === false) {
                $table = $base;
                $column = $col;
            } else {
                [$table, $column] = explode('.', $col, 2);
            }

            if (!$this->isAllowedTable($table) || !$this->isAllowedColumn($table, $column)) {
                throw new \InvalidArgumentException("Coluna não permitida: {$col}");
            }

            if ($alias) {
                $out[] = "{$table}.{$column} as {$alias}";
            } else {
                $safe = "{$table}__{$column}";
                $out[] = "{$table}.{$column} as {$safe}";
            }
        }
        return $out;
    }

    // coleta tabelas pedidas pelo select + joins explicitados (para montar caminho)
    protected function collectRequestedTables(array $payload, string $base): array
    {
        $tables = [$base];
        foreach ($payload['select'] ?? [] as $s) {
            $col = preg_split('/\s+as\s+/i', $s)[0];
            if (strpos($col, '.') !== false) {
                [$t] = explode('.', $col, 2);
                $tables[] = $t;
            }
        }

        // adiciona joins explicitos (select de UI pode enviar joins[] com as tabelas selecionadas)
        foreach ($payload['joins'] ?? [] as $j) {
            $tables[] = $j;
        }

        return array_values(array_unique($tables));
    }

    /**
     * run(payload, $limit)
     * $limit: number (per page) | 'all'
     */
    public function run(array $payload, $limit = null)
{
    $base = $payload['base'];
    if (!$this->isAllowedTable($base)) abort(403, 'Tabela base não permitida');

    // preparar selects básicos (sem alias ainda)
    $rawSelects = $payload['select'] ?? [];
    // se vazio => lista todas as colunas base
    if (empty($rawSelects)) {
        $rawSelects = array_map(fn($c) => "{$base}.{$c}", $this->config['tables'][$base]['columns'] ?? []);
    }

    // coletar tabelas necessárias
    $needed = $this->collectRequestedTables($payload, $base);

    $qb = DB::table($base);
    $joined = [$base];

    // aplicar joins em cadeia (mesma lógica findPath que já temos)
    foreach ($needed as $t) {
        if ($t === $base || in_array($t, $joined)) continue;
        $path = $this->findPath($base, $t);
        if (!$path) abort(403, "Relação não definida entre {$base} e {$t}");
        for ($i = 0; $i < count($path) - 1; $i++) {
            $left = $path[$i];
            $right = $path[$i+1];
            if (in_array($right, $joined)) continue;
            $rel = $this->getRelationBetween($left, $right);
            if (!$rel) abort(500, "Relação mal definida entre {$left} e {$right}");
            [$onLeft, $onRight] = $rel['on'];
            $type = $rel['type'] ?? 'inner';
            if ($type === 'left') $qb->leftJoin($right, $onLeft, '=', $onRight);
            else $qb->join($right, $onLeft, '=', $onRight);
            $joined[] = $right;
        }
    }

    // filtros (mantém lógica anterior)
    foreach ($payload['filters'] ?? [] as $f) {
        if (empty($f['column'])) continue;
        $col = $f['column'];
        $op = strtolower($f['operator'] ?? '=');
        $val = $f['value'] ?? null;
        [$t, $c] = explode('.', $col, 2);
        if (!$this->isAllowedTable($t) || !$this->isAllowedColumn($t, $c)) abort(403, "Filtro em coluna não permitida: {$col}");
        if ($op === 'in' && is_array($val)) $qb->whereIn($col, $val);
        elseif ($op === 'like') $qb->where($col, 'like', "%{$val}%");
        else {
            if (!in_array($op, ['=','!=','>','<','>=','<='], true)) abort(403, 'Operador inválido');
            $qb->where($col, $op, $val);
        }
    }

    // decide se vamos agrupar por base (true por padrão para evitar duplicados)
    $groupByBase = $payload['group_by_base'] ?? true;

    // prepara selects: se agrupando por base, transformar selects em agregados ANY_VALUE(...) as alias
    $selectsSql = [];
    foreach ($rawSelects as $s) {
        // já pode vir com alias "table.column as alias"
        $parts = preg_split('/\s+as\s+/i', $s);
        $col = trim($parts[0]);
        $alias = $parts[1] ?? null;

        // tratar se o usuário já passou função agregada (COUNT/SUM/AVG/GROUP_CONCAT/ANY_VALUE etc.)
        $isAggregated = preg_match('/\b(COUNT|SUM|AVG|MIN|MAX|GROUP_CONCAT|ANY_VALUE)\s*\(/i', $col);

        if ($groupByBase && !$isAggregated) {
            // construir ANY_VALUE(table.column) as table__column (ou alias)
            if (strpos($col, '.') === false) {
                $col = "{$base}.{$col}";
            }
            $safeAlias = $alias ?? str_replace('.', '__', $col);
            $selectsSql[] = DB::raw("ANY_VALUE({$col}) as `{$safeAlias}`");
        } else {
            // não agrupa, simplesmente usar select normal (respeita alias)
            if (strpos($col, '.') === false) $col = "{$base}.{$col}";
            if ($alias) $selectsSql[] = DB::raw("{$col} as {$alias}");
            else {
                $safeAlias = str_replace('.', '__', $col);
                $selectsSql[] = DB::raw("{$col} as `{$safeAlias}`");
            }
        }
    }

    // aplicar selects
    $qb->select($selectsSql);

    // calcular total_count com base.distinct
    $clone = clone $qb;
    try {
        $total_count = $clone->distinct()->count("{$base}.id");
    } catch (\Throwable $e) {
        // fallback
        $total_count = $clone->get()->count();
    }

    // se o usuário pediu 'all' retorna coleção completa (sem paginação)
    if ($limit === 'all') {
        $items = $qb->get();
        // adicionar total_count em metadados não é necessário — controller calcula
        return $items;
    }

    // paginação simples
    $perPage = is_numeric($limit) ? intval($limit) : ($payload['limit'] ?? $this->config['default_limit']);
    $page = (int) request()->get('page', 1);
    $offset = ($page - 1) * $perPage;
    $items = $qb->offset($offset)->limit($perPage)->get();

    return new LengthAwarePaginator($items->values(), $total_count, $perPage, $page, [
        'path' => request()->url(),
        'query' => request()->query(),
    ]);
}
}