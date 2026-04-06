<?php
namespace App\Models\Traits;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

trait Reportable
{
    /**
     * Nome amigável da Entidade (tradução ou fallback "humanizado")
     */
    public static function getReportLabel()
    {
        $key = 'database.models.' . class_basename(get_called_class());
        $label = __($key);

        // se não houver tradução, o __() retorna a própria chave,
        // então detectamos e fazemos um fallback legível
        if ($label === $key) {
            return Str::title(Str::snake(class_basename(get_called_class()), ' '));
        }

        return $label;
    }

    /**
     * Colunas que NUNCA devem aparecer (Segurança)
     */
    public static function getBlacklist()
    {
        return ['password', 'remember_token', 'deleted_at'];
    }

    /**
     * Opcional: lista explícita de colunas que o model quer expor no relatório.
     * Se retornar null, expõe todas (exceto blacklist). Se retornar array, usa apenas estas (na ordem dada).
     *
     * Exemplo:
     *   return ['registration', 'student_code', 'person.name'];
     */
    public static function getReportColumns(): ?array
    {
        return null; // padrão = null (todas as colunas)
    }

    /**
     * Opcional: mapeamento de labels por coluna.
     * Chave = nome da coluna (ex: 'registration' ou 'person.name'), valor = label amigável.
     * Exemplo:
     *   return ['registration' => 'Matrícula', 'person.name' => 'Nome do Aluno'];
     */
    public static function getReportColumnLabels(): array
    {
        return [];
    }

    /**
     * Retorna colunas traduzidas / amigáveis da tabela do model.
     * - Prioriza getReportColumnLabels() (overrides definidos no model)
     * - Depois tenta arquivo de tradução "database.columns.{table}.{col}"
     * - Finalmente fallback humanizado (Title Case)
     *
     * Se o model definiu getReportColumns() (array), apenas essas colunas são retornadas (na ordem).
     */
    public static function getTranslatedColumns()
    {
        $instance = new static;
        $table = $instance->getTable();
        $blacklist = static::getBlacklist();

        // todas as colunas do schema (sem blacklist)
        $all = collect(Schema::getColumnListing($table))
            ->reject(fn($c) => in_array($c, $blacklist))
            ->values()
            ->all();

        // se o model declarou explicitamente as colunas a expor, respeitar essa lista
        $declared = static::getReportColumns();
        if (is_array($declared) && !empty($declared)) {
            // filtrar apenas as colunas válidas do schema (descarta inválidas)
            $schemaCols = array_filter($declared, function($c) use ($all) {
                // permitir colunas compostas (relation.col) também aqui — não removemos
                if (str_contains($c, '.')) return true;
                return in_array($c, $all);
            });
            $cols = array_values($schemaCols);
        } else {
            $cols = $all;
        }

        // overrides vindos do model (labels customizados)
        $overrides = static::getReportColumnLabels() ?: [];

        $result = [];
        foreach ($cols as $col) {
            // se for chave composta (relation.col) não tentamos tradução por table
            if (str_contains($col, '.')) {
                // se o model definiu um label explícito para essa chave composta, usar
                $label = $overrides[$col] ?? Str::title(str_replace(['_', '.'], [' ', ' › '], $col));
            } else {
                // tentar override -> tradução por arquivo -> fallback humanizado
                if (array_key_exists($col, $overrides)) {
                    $label = $overrides[$col];
                } else {
                    $transKey = "database.columns.{$table}.{$col}";
                    $trans = __($transKey);
                    $label = ($trans === $transKey) ? Str::title(str_replace('_', ' ', $col)) : $trans;
                }
            }

            $result[$col] = $label;
        }

        return collect($result);
    }

    /**
     * Lista (opcional) de relações que devem ser embutidas automaticamente
     * Ex: return ['person'];  // injeta person.* como columns 'person.field'
     */
    public static function getEmbeddedRelations(): array
    {
        return [];
    }
}