<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    protected function relationAliasForMorphTarget(string $relationName, string $targetClass): string
    {
        return $relationName . '_' . \Illuminate\Support\Str::snake(class_basename($targetClass));
    }

    protected function getMorphAliasMap(string $modelClass): array
    {
        if (!method_exists($modelClass, 'getReportMorphTargets')) {
            return [];
        }

        $map = [];

        foreach ((array) $modelClass::getReportMorphTargets() as $relationName => $targets) {
            foreach ((array) $targets as $targetClass) {
                if (!class_exists($targetClass)) {
                    continue;
                }

                $alias = $this->relationAliasForMorphTarget($relationName, $targetClass);

                $map[$alias] = [
                    'base_relation' => $relationName,
                    'target_class' => $targetClass,
                    'morph_type' => (new $targetClass)->getMorphClass(),
                ];
            }
        }

        return $map;
    }

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

    /*
    |--------------------------------------------------------------------------
    | BASE COLUMNS
    |--------------------------------------------------------------------------
    */

    $baseColumns = $modelClass::getTranslatedColumns();

    $columns = $baseColumns->toArray();

    /*
    |--------------------------------------------------------------------------
    | DETECTA SE O MODEL DEFINIU COLUNAS EXPLÍCITAS
    |--------------------------------------------------------------------------
    */

    $declaredColumns = null;

    if (method_exists($modelClass, 'getReportColumns')) {
        $declaredColumns = $modelClass::getReportColumns();
    }

    $hasDeclaredColumns =
        is_array($declaredColumns)
        && !empty($declaredColumns);

    /*
    |--------------------------------------------------------------------------
    | RELAÇÕES PERMITIDAS PARA EMBED
    |--------------------------------------------------------------------------
    */

    $allowedEmbedded = [];
    $allowedCollections = [];

    if (
        !$hasDeclaredColumns
        && is_callable([$modelClass, 'getEmbeddedRelations'])
    ) {
        $allowedEmbedded = (array) $modelClass::getEmbeddedRelations();
    }

    if (method_exists($modelClass, 'getReportCollectionRelations')) {
        $allowedCollections = (array) $modelClass::getReportCollectionRelations();
    }

    /*
    |--------------------------------------------------------------------------
    | INSPEÇÃO DAS RELAÇÕES
    |--------------------------------------------------------------------------
    */

    $relations = [];
    $morphTargets = method_exists($modelClass, 'getReportMorphTargets')
        ? (array) $modelClass::getReportMorphTargets()
        : [];

    $reflector = new \ReflectionClass($modelClass);

    foreach ($reflector->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {

        if (
            $method->class !== $reflector->getName()
            || $method->getNumberOfParameters() > 0
        ) {
            continue;
        }

        try {
            $return = $method->invoke($model);
        } catch (\Throwable $e) {
            continue;
        }

        if (!$return instanceof \Illuminate\Database\Eloquent\Relations\Relation) {
            continue;
        }

        $relationName = $method->getName();

        $relation = $model->$relationName();

        if ($relation instanceof \Illuminate\Database\Eloquent\Relations\MorphMany) {
            continue;
        }

        if (
            $relation instanceof \Illuminate\Database\Eloquent\Relations\HasMany
            && !in_array($relationName, $allowedCollections, true)
        ) {
            continue;
        }

        if ($relation instanceof \Illuminate\Database\Eloquent\Relations\MorphTo) {
            $targets = (array) ($morphTargets[$relationName] ?? []);

            foreach ($targets as $targetClass) {
                if (!class_exists($targetClass)) {
                    continue;
                }

                if (
                    !in_array(
                        \App\Models\Traits\Reportable::class,
                        class_uses_recursive($targetClass)
                    )
                ) {
                    continue;
                }

                $targetModel = new $targetClass;
                $relTable = $targetModel->getTable();
                $relCols = [];

                if (is_callable([$targetClass, 'getTranslatedColumns'])) {
                    try {
                        $relCols = $targetClass::getTranslatedColumns()->toArray();
                    } catch (\Throwable $e) {
                        $relCols = [];
                    }
                }

                if (empty($relCols)) {
                    $blacklist = method_exists($targetClass, 'getBlacklist')
                        ? $targetClass::getBlacklist()
                        : ['password', 'remember_token', 'deleted_at'];

                    $relCols = collect(Schema::getColumnListing($relTable))
                        ->reject(fn($c) => in_array($c, $blacklist))
                        ->mapWithKeys(function ($c) use ($relTable) {
                            $transKey = "database.columns.{$relTable}.{$c}";
                            $trans = __($transKey);

                            $label = ($trans === $transKey)
                                ? \Illuminate\Support\Str::title(str_replace('_', ' ', $c))
                                : $trans;

                            return [$c => $label];
                        })
                        ->toArray();
                }

                $relations[] = [
                    'name' => $this->relationAliasForMorphTarget($relationName, $targetClass),
                    'type' => class_basename(get_class($relation)),
                    'related_class' => $targetClass,
                    'label' => is_callable([$targetClass, 'getReportLabel'])
                        ? $targetClass::getReportLabel()
                        : class_basename($targetClass),
                    'table' => $relTable,
                    'columns' => $relCols,
                    'morph' => [
                        'base_relation' => $relationName,
                        'target_class' => $targetClass,
                    ],
                ];
            }

            continue;
        }

        $related = $relation->getRelated();

        $relatedClass = get_class($related);

        $relTable = $related->getTable();

        /*
        |--------------------------------------------------------------------------
        | SÓ PERMITE RELAÇÕES COM MODELS REPORTABLE
        |--------------------------------------------------------------------------
        */

        if (
            !in_array(
                \App\Models\Traits\Reportable::class,
                class_uses_recursive($relatedClass)
            )
        ) {
            continue;
        }

        /*
        |--------------------------------------------------------------------------
        | COLUNAS DO RELATED MODEL
        |--------------------------------------------------------------------------
        */

        $relCols = [];

        if (is_callable([$relatedClass, 'getTranslatedColumns'])) {
            try {
                $relCols = $relatedClass::getTranslatedColumns()->toArray();
            } catch (\Throwable $e) {
                $relCols = [];
            }
        }

        if (empty($relCols)) {

            $blacklist = method_exists($relatedClass, 'getBlacklist')
                ? $relatedClass::getBlacklist()
                : ['password', 'remember_token', 'deleted_at'];

            $relCols = collect(
                Schema::getColumnListing($relTable)
            )
                ->reject(fn($c) => in_array($c, $blacklist))
                ->mapWithKeys(function ($c) use ($relTable) {

                    $transKey = "database.columns.{$relTable}.{$c}";

                    $trans = __($transKey);

                    $label =
                        ($trans === $transKey)
                        ? \Illuminate\Support\Str::title(
                            str_replace('_', ' ', $c)
                        )
                        : $trans;

                    return [$c => $label];
                })
                ->toArray();
        }

        /*
        |--------------------------------------------------------------------------
        | BASE RELATION DATA
        |--------------------------------------------------------------------------
        */

        $relData = [

            'name' => $relationName,

            'type' => class_basename(get_class($relation)),

            'related_class' => $relatedClass,

            'label' => is_callable([$relatedClass, 'getReportLabel'])
                ? $relatedClass::getReportLabel()
                : class_basename($relatedClass),

            'table' => $relTable,

            'columns' => $relCols,
        ];

        /*
        |--------------------------------------------------------------------------
        | PIVOT SUPPORT (BelongsToMany)
        |--------------------------------------------------------------------------
        */

        if (
            $relation instanceof
            \Illuminate\Database\Eloquent\Relations\BelongsToMany
        ) {

            $pivotTable = $relation->getTable();

            $pivotColumns = [];

            if (method_exists($relation, 'getPivotColumns')) {
                try {
                    $pivotColumns = $relation->getPivotColumns();
                } catch (\Throwable $e) {
                    $pivotColumns = [];
                }
            }

            if (empty($pivotColumns)) {

                $allPivotCols =
                    Schema::getColumnListing($pivotTable);

                $foreign1 =
                    method_exists(
                        $relation,
                        'getForeignPivotKeyName'
                    )
                    ? $relation->getForeignPivotKeyName()
                    : null;

                $foreign2 =
                    method_exists(
                        $relation,
                        'getRelatedPivotKeyName'
                    )
                    ? $relation->getRelatedPivotKeyName()
                    : null;

                $exclude = array_filter([
                    $foreign1,
                    $foreign2,
                    'id',
                    'created_at',
                    'updated_at',
                ]);

                $pivotColumns = array_values(
                    array_filter(
                        $allPivotCols,
                        fn($c) => !in_array($c, $exclude)
                    )
                );
            }

            $pivotColumns = array_values(
                array_filter(
                    $pivotColumns,
                    fn($c) => !in_array(
                        $c,
                        ['created_at', 'updated_at']
                    )
                )
            );

            $pivotColsLabels = [];
            $pivotLabelOverrides = [];

            if (method_exists($relation, 'getPivotClass')) {
                $pivotClass = $relation->getPivotClass();

                if (is_string($pivotClass) && class_exists($pivotClass)) {
                    if (method_exists($pivotClass, 'getAuditLabels')) {
                        $pivotLabelOverrides = (array) $pivotClass::getAuditLabels();
                    } elseif (method_exists($pivotClass, 'getReportColumnLabels')) {
                        $pivotLabelOverrides = (array) $pivotClass::getReportColumnLabels();
                    }
                }
            }

            foreach ($pivotColumns as $c) {
                if (isset($pivotLabelOverrides[$c])) {
                    $pivotColsLabels[$c] = $pivotLabelOverrides[$c];
                    continue;
                }

                $transKey =
                    "database.columns.{$pivotTable}.{$c}";

                $trans = __($transKey);

                $label =
                    ($trans === $transKey)
                    ? \Illuminate\Support\Str::title(
                        str_replace('_', ' ', $c)
                    )
                    : $trans;

                $pivotColsLabels[$c] = $label;
            }

            if (!empty($pivotColsLabels)) {

                $relData['pivot'] = [

                    'table' => $pivotTable,

                    'columns' => $pivotColsLabels,
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | EMBED CONTROL (FIX CRÍTICO)
        |--------------------------------------------------------------------------
        */

        $isSingular =
            $relation instanceof
                \Illuminate\Database\Eloquent\Relations\BelongsTo
            || $relation instanceof
                \Illuminate\Database\Eloquent\Relations\HasOne
            || $relation instanceof
                \Illuminate\Database\Eloquent\Relations\MorphOne;

        $shouldEmbed =
            !$hasDeclaredColumns
            && $isSingular
            && in_array($relationName, $allowedEmbedded);

        if ($shouldEmbed) {

            foreach ($relCols as $colKey => $colLabel) {

                $composedKey =
                    "{$relationName}.{$colKey}";

                if (!array_key_exists(
                    $composedKey,
                    $columns
                )) {

                    $columns[$composedKey] =
                        $colLabel;
                }
            }
        }

        $relations[] = $relData;
    }

    return response()->json([

        'class' => $modelClass,

        'label' => $modelClass::getReportLabel(),

        'table' => $table,

        'columns' => $columns,

        'relations' => $relations,
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
        $morphAliasMap = $this->getMorphAliasMap($modelClass);
        $selectedMorphTargets = [];
        foreach (array_merge($selected, array_column($filters, 'column')) as $col) {
            if (!str_contains($col ?? '', '.')) {
                continue;
            }

            $relationName = explode('.', $col)[0];
            $relationsToLoad[] = $morphAliasMap[$relationName]['base_relation'] ?? $relationName;

            if (isset($morphAliasMap[$relationName])) {
                $baseRelation = $morphAliasMap[$relationName]['base_relation'];
                $morphType = $morphAliasMap[$relationName]['morph_type'];
                $selectedMorphTargets[$baseRelation] ??= [];
                $selectedMorphTargets[$baseRelation][$morphType] = $morphType;
            }
        }
        $relationsToLoad = array_values(array_unique(array_filter($relationsToLoad)));

        if ($relationsToLoad) $query->with($relationsToLoad);

        foreach ($selectedMorphTargets as $baseRelation => $targetClasses) {
            $query->whereHasMorph($baseRelation, array_values($targetClasses));
        }

        // filtros
        foreach ($filters as $f) {
            $col = $f['column'] ?? null;
            $op  = $f['operator'] ?? '=';
            $val = $f['value'] ?? null;
            if (!$col || $val === null || $val === '') continue;

            if (str_contains($col, '.')) {
                [$relation, $relCol] = explode('.', $col, 2);

                if (isset($morphAliasMap[$relation])) {
                    $baseRelation = $morphAliasMap[$relation]['base_relation'];
                    $morphType = $morphAliasMap[$relation]['morph_type'];

                    $query->whereHasMorph($baseRelation, [$morphType], fn($q) =>
                        strtolower($op) === 'like'
                            ? $q->where($relCol, 'like', "%{$val}%")
                            : $q->where($relCol, $op, $val)
                    );
                } else {
                    $query->whereHas($relation, fn($q) =>
                        strtolower($op) === 'like'
                            ? $q->where($relCol, 'like', "%{$val}%")
                            : $q->where($relCol, $op, $val)
                    );
                }
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

                $value = data_get($row, $colKey);

                if ($value === null && str_contains($colKey, '.')) {
                    [$relationName, $relCol] = explode('.', $colKey, 2);

                    if (isset($morphAliasMap[$relationName])) {
                        $baseRelation = $morphAliasMap[$relationName]['base_relation'];
                        $targetClass = $morphAliasMap[$relationName]['target_class'];
                        $relationValue = $row->$baseRelation ?? null;

                        if (!$relationValue instanceof $targetClass) {
                            $relationValue = null;
                        }
                    } else {
                        $relationValue = $row->$relationName ?? null;
                    }

                    if ($relationValue instanceof \Illuminate\Support\Collection) {
                        $value = $relationValue
                            ->map(fn($item) => data_get($item, $relCol))
                            ->filter()
                            ->unique()
                            ->values()
                            ->implode(', ');
                    } elseif ($relationValue) {
                        $value = data_get($relationValue, $relCol);
                    }
                }

                if ($value instanceof \Illuminate\Support\Collection) {
                    $value = $value->filter()->unique()->values()->implode(', ');
                }

                if (is_bool($value)) {
                    $value = $value ? 'Sim' : 'Não';
                }

                if ($value instanceof \BackedEnum) {
                    $value = method_exists($value, 'label') ? $value->label() : $value->value;
                }

                if ($value instanceof \Carbon\CarbonInterface) {
                    $hasTime = $value->hour > 0 || $value->minute > 0 || $value->second > 0;
                    $value = $value->format($hasTime ? 'd/m/Y H:i' : 'd/m/Y');
                }

                if (is_object($value) && !method_exists($value, '__toString')) {
                    $value = json_encode($value);
                }

                $out[$alias] = $value;
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
