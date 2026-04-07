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

    if (
        !$hasDeclaredColumns
        && is_callable([$modelClass, 'getEmbeddedRelations'])
    ) {
        $allowedEmbedded = (array) $modelClass::getEmbeddedRelations();
    }

    /*
    |--------------------------------------------------------------------------
    | INSPEÇÃO DAS RELAÇÕES
    |--------------------------------------------------------------------------
    */

    $relations = [];

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

            foreach ($pivotColumns as $c) {

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

                $value = data_get($row, $colKey);

                if ($value instanceof \Illuminate\Support\Collection) {

                    $value = $value
                        ->filter()
                        ->unique()
                        ->values()
                        ->implode(', ');
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