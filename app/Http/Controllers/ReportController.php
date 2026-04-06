<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    // Página do builder
    public function builder()
    {
        return view('reports.builder');
    }

    // Lista todas as entidades Reportable (procura na pasta Models)
    public function availableEntities()
    {
        $modelsPath = app_path('Models');
        $files = collect(File::allFiles($modelsPath));

        $entities = $files->map(function($f) use ($modelsPath) {
            $relative = str_replace([$modelsPath . DIRECTORY_SEPARATOR, '.php'], '', $f->getPathname());
            $class = 'App\\Models\\' . str_replace(DIRECTORY_SEPARATOR, '\\', $relative);
            if (!class_exists($class)) return null;

            // só retorna se usar o trait Reportable
            if (!in_array(\App\Models\Traits\Reportable::class, class_uses_recursive($class))) return null;

            return [
                'class' => $class,
                'label' => $class::getReportLabel()
            ];
        })->filter()->values();

        return response()->json($entities);
    }

    // Meta (colunas + relações) para uma entidade específica
    public function meta(Request $request)
    {
        $modelClass = $request->input('model');
        if (!$modelClass || !class_exists($modelClass)) {
            return response()->json(['error' => 'Modelo inválido'], 400);
        }

        if (!in_array(\App\Models\Traits\Reportable::class, class_uses_recursive($modelClass))) {
            return response()->json(['error' => 'Modelo não reportável'], 403);
        }

        $model = new $modelClass;
        $table = $model->getTable();

        // colunas da tabela base (respeitando traduções / overrides do model)
        $baseColumns = $modelClass::getTranslatedColumns(); // collection col => label
        $columns = $baseColumns->toArray();

        // lista de relações que podem ser embutidas automaticamente (opcional no model)
        $allowedEmbedded = [];
        if (is_callable([$modelClass, 'getEmbeddedRelations'])) {
            $allowedEmbedded = (array) $modelClass::getEmbeddedRelations();
        }

        $relations = [];
        $reflector = new \ReflectionClass($modelClass);

        foreach ($reflector->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            // ignora métodos herdados e com parâmetros
            if ($method->class !== $reflector->getName() || $method->getNumberOfParameters() > 0) {
                continue;
            }

            try {
                $return = $method->invoke($model);
            } catch (\Throwable $e) {
                continue;
            }

            if ($return instanceof \Illuminate\Database\Eloquent\Relations\Relation) {
                $relationName = $method->getName();
                $relation = $model->$relationName();
                $related = $relation->getRelated();
                $relatedClass = get_class($related);
                $relTable = $related->getTable();

                // somente incluir relações cujo related model use Reportable
                if (!in_array(\App\Models\Traits\Reportable::class, class_uses_recursive($relatedClass))) {
                    continue;
                }

                // obter colunas do related (respeitando getTranslatedColumns do related quando existir)
                $relCols = [];
                if (is_callable([$relatedClass, 'getTranslatedColumns'])) {
                    try {
                        $relCols = $relatedClass::getTranslatedColumns()->toArray();
                    } catch (\Throwable $e) {
                        $relCols = [];
                    }
                }

                if (empty($relCols)) {
                    $relCols = collect(Schema::getColumnListing($relTable))
                        ->reject(fn($c) => in_array($c, method_exists($relatedClass, 'getBlacklist') ? $relatedClass::getBlacklist() : ['password', 'remember_token', 'deleted_at']))
                        ->mapWithKeys(fn($c) => [
                            $c => __("database.columns.{$relTable}.{$c}") !== "database.columns.{$relTable}.{$c}"
                                ? __("database.columns.{$relTable}.{$c}")
                                : \Illuminate\Support\Str::title(str_replace('_', ' ', $c))
                        ])
                        ->toArray();
                }

                $relData = [
                    'name' => $relationName,
                    'type' => class_basename(get_class($relation)),
                    'related_class' => $relatedClass,
                    'label' => (is_callable([$relatedClass, 'getReportLabel']) ? $relatedClass::getReportLabel() : class_basename($relatedClass)),
                    'table' => $relTable,
                    'columns' => $relCols
                ];

                // === Tratamento de pivot (somente para BelongsToMany) ===
                if ($relation instanceof \Illuminate\Database\Eloquent\Relations\BelongsToMany) {
                    $pivotTable = $relation->getTable();

                    // tenta obter colunas declaradas em withPivot()
                    $pivotColumns = [];
                    if (method_exists($relation, 'getPivotColumns')) {
                        try {
                            $pivotColumns = $relation->getPivotColumns(); // geralmente retorna array
                        } catch (\Throwable $e) {
                            $pivotColumns = [];
                        }
                    }

                    // se não houver withPivot(), tenta inferir a partir do schema, excluindo chaves FK e campos técnicos
                    if (empty($pivotColumns)) {
                        $allPivotCols = collect(Schema::getColumnListing($pivotTable))->values()->all();

                        // nomes das chaves pivot (ex: student_id, deficiency_id)
                        $foreign1 = method_exists($relation, 'getForeignPivotKeyName') ? $relation->getForeignPivotKeyName() : null;
                        $foreign2 = method_exists($relation, 'getRelatedPivotKeyName') ? $relation->getRelatedPivotKeyName() : null;

                        $exclude = array_filter([$foreign1, $foreign2, 'id', 'created_at', 'updated_at']);

                        $pivotColumns = array_values(array_filter($allPivotCols, fn($c) => !in_array($c, $exclude)));
                    }

                    // filtrar campos inúteis (timestamps e similares)
                    $pivotColumns = array_values(array_filter($pivotColumns, fn($c) => !in_array($c, ['created_at', 'updated_at'])));

                    // transformar em label traduzida/fallback
                    $pivotColsLabels = [];
                    foreach ($pivotColumns as $c) {
                        $transKey = "database.columns.{$pivotTable}.{$c}";
                        $trans = __($transKey);
                        $label = ($trans === $transKey) ? \Illuminate\Support\Str::title(str_replace('_', ' ', $c)) : $trans;
                        $pivotColsLabels[$c] = $label;
                    }

                    // só expõe pivot se houver campos "úteis" (ex: severity, notes...). NÃO expõe se só restarem chaves FK/ids.
                    if (!empty($pivotColsLabels)) {
                        $relData['pivot'] = [
                            'table' => $pivotTable,
                            'columns' => $pivotColsLabels
                        ];
                    }
                }

                // --- embed singular relations (belongsTo/hasOne/morphOne) conforme allowedEmbedded
                $isSingular = $relation instanceof \Illuminate\Database\Eloquent\Relations\BelongsTo
                    || $relation instanceof \Illuminate\Database\Eloquent\Relations\HasOne
                    || $relation instanceof \Illuminate\Database\Eloquent\Relations\MorphOne;

                $shouldEmbed = $isSingular && (empty($allowedEmbedded) || in_array($relationName, $allowedEmbedded));

                if ($shouldEmbed && isset($relData['columns']) && is_array($relData['columns'])) {
                    foreach ($relData['columns'] as $colKey => $colLabel) {
                        $composedKey = "{$relationName}.{$colKey}";
                        if (!array_key_exists($composedKey, $columns)) {
                            $columns[$composedKey] = $colLabel;
                        }
                    }
                }

                $relations[] = $relData;
            }
        }

        return response()->json([
            'class' => $modelClass,
            'label' => $modelClass::getReportLabel(),
            'table' => $table,
            'columns' => $columns,
            'relations' => $relations
        ]);
    }

    // dentro de ReportController (substitua o método run existing)
public function run(Request $request)
{
    try {
        $modelClass = $request->input('model');
        $selected   = $request->input('columns', []);
        $filters    = $request->input('filters', []);
        $limit      = intval($request->input('limit', 200));

        if (!$modelClass || !class_exists($modelClass))
            return response()->json(['error' => 'Modelo inválido'], 400);

        if (!in_array(\App\Models\Traits\Reportable::class, class_uses_recursive($modelClass)))
            return response()->json(['error' => 'Modelo não reportável'], 403);

        $query = $modelClass::query();

        // relações necessárias (vindas de colunas selecionadas e filtros)
        $relationsToLoad = [];
        foreach (array_merge($selected, array_column($filters, 'column')) as $col) {
            if (str_contains($col ?? '', '.'))
                $relationsToLoad[] = explode('.', $col)[0];
        }
        $relationsToLoad = array_values(array_unique(array_filter($relationsToLoad)));

        if ($relationsToLoad) $query->with($relationsToLoad);

        // filtros
        foreach ($filters as $f) {
            $col = $f['column'] ?? null;
            $op  = $f['operator'] ?? '=';
            $val = $f['value'] ?? null;
            if (!$col || $val === null || $val === '') continue;

            if (str_contains($col, '.')) {
                [$relation, $relCol] = explode('.', $col, 2);
                $query->whereHas($relation, fn($q) =>
                    strtolower($op) === 'like'
                        ? $q->where($relCol, 'like', "%{$val}%")
                        : $q->where($relCol, $op, $val)
                );
            } else {
                strtolower($op) === 'like'
                    ? $query->where($col, 'like', "%{$val}%")
                    : $query->where($col, $op, $val);
            }
        }

        $rows   = $query->limit($limit)->get();
        $total  = $rows->count();
        $result = [];

        foreach ($rows as $row) {
            $out = [];
            foreach ($selected as $colKey) {
                $alias = str_replace('.', '__', $colKey);
                if (!str_contains($colKey, '.')) {
                    $out[$alias] = $row->{$colKey} ?? null;
                } else {
                    $parts   = explode('.', $colKey);
                    $related = $row->{$parts[0]};

                    if (count($parts) === 3 && $parts[1] === 'pivot') {
                        $vals = [];
                        if ($related)
                            foreach ($related as $r)
                                if (isset($r->pivot->{$parts[2]}))
                                    $vals[] = $r->pivot->{$parts[2]};
                        $out[$alias] = $vals ? implode(', ', array_unique($vals)) : null;
                    } elseif ($related instanceof \Illuminate\Database\Eloquent\Collection) {
                        $vals = $related->pluck($parts[1])->filter()->unique()->values()->all();
                        $out[$alias] = $vals ? implode(', ', $vals) : null;
                    } elseif (is_object($related)) {
                        $out[$alias] = $related->{$parts[1]} ?? null;
                    } else {
                        $out[$alias] = null;
                    }
                }
            }
            $result[] = $out;
        }

        return response()->json([
            'rows'  => $result,
            'total' => $total,
        ]);

    } catch (\Throwable $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'line'  => $e->getLine(),
            'file'  => $e->getFile(),
        ], 500);
    }
}

    // Exporta para PDF (recebe mesmo formato do run)
    public function exportPdf(Request $request)
    {
        $modelClass = $request->input('model');
        $selected = $request->input('columns', []);
        $filters = $request->input('filters', []);
        $labels = $request->input('labels', []);

        // reutiliza a lógica de run mas sem limite baixo (ou com limitação)
        $request->merge(['limit' => 1000]);
        $resp = $this->run($request);
        $data = $resp->getData();
        $rows = $data->rows ?? [];

        $pdf = Pdf::loadView('reports.pdf', [
            'data' => $rows,
            'headers' => $labels
        ]);

        return $pdf->download('relatorio.pdf');
    }
}