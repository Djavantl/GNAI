<?php

declare(strict_types=1);

namespace App\Domains\Audit\Domain\ValueObjects;

use App\Domains\Audit\Domain\Enums\FieldFormat;
use BackedEnum;
use Illuminate\Database\Eloquent\Model;

/** Um campo de um model auditável: o rótulo em linguagem simples e o formato. */
final readonly class AuditedField
{
    /**
     * @param  class-string<BackedEnum>|null  $enumClass  formatos ENUM e LIST
     * @param  class-string<Model>|null  $referencedModel  formatos REFERENCE e LIST de registros
     * @param  string|null  $nameColumn  coluna do model referenciado que o identifica para as pessoas, ou
     *                                   caminho por relação quando o nome mora em outra tabela ('person.name')
     * @param  array<string, string>  $valueLabels  rótulos de valores gravados como texto livre
     */
    private function __construct(
        public string $label,
        public FieldFormat $format,
        public ?string $enumClass = null,
        public ?string $referencedModel = null,
        public ?string $nameColumn = null,
        public array $valueLabels = [],
    ) {}

    public static function text(string $label): self
    {
        return new self($label, FieldFormat::TEXT);
    }

    public static function richText(string $label): self
    {
        return new self($label, FieldFormat::RICH_TEXT);
    }

    public static function number(string $label): self
    {
        return new self($label, FieldFormat::NUMBER);
    }

    public static function money(string $label): self
    {
        return new self($label, FieldFormat::MONEY);
    }

    public static function date(string $label): self
    {
        return new self($label, FieldFormat::DATE);
    }

    public static function dateTime(string $label): self
    {
        return new self($label, FieldFormat::DATE_TIME);
    }

    public static function time(string $label): self
    {
        return new self($label, FieldFormat::TIME);
    }

    public static function boolean(string $label): self
    {
        return new self($label, FieldFormat::BOOLEAN);
    }

    /**
     * Enum PHP com `label()` ou, sem enum, um mapa valor → rótulo para colunas de texto com valores fixos.
     *
     * @param  class-string<BackedEnum>|null  $enum
     * @param  array<string, string>  $valueLabels
     */
    public static function enum(string $label, ?string $enum = null, array $valueLabels = []): self
    {
        return new self($label, FieldFormat::ENUM, enumClass: $enum, valueLabels: $valueLabels);
    }

    /** @param  class-string<Model>  $model */
    public static function reference(string $label, string $model, string $nameColumn = 'name'): self
    {
        return new self($label, FieldFormat::REFERENCE, referencedModel: $model, nameColumn: $nameColumn);
    }

    /**
     * Lista de valores de um enum ou de ids de registros (pivôs). Com `$model`, a leitura mostra os nomes.
     *
     * @param  class-string<BackedEnum>|null  $enum
     * @param  class-string<Model>|null  $model
     */
    public static function list(string $label, ?string $enum = null, ?string $model = null, string $nameColumn = 'name'): self
    {
        return new self(
            $label,
            FieldFormat::LIST,
            enumClass: $enum,
            referencedModel: $model,
            nameColumn: $model === null ? null : $nameColumn,
        );
    }

    public static function file(string $label): self
    {
        return new self($label, FieldFormat::FILE);
    }

    public static function secret(string $label): self
    {
        return new self($label, FieldFormat::SECRET);
    }

    /**
     * Valor gravado → texto para pessoas. Na referência vale o nome guardado no momento da mudança.
     */
    public function format(mixed $stored, int|string|null $referencedName = null): ?string
    {
        if ($this->format === FieldFormat::REFERENCE) {
            return $referencedName !== null
                ? (string) $referencedName
                : ($stored === null ? null : "#{$stored}");
        }

        if ($stored === null) {
            return null;
        }

        return $this->format === FieldFormat::ENUM
            ? $this->valueLabel($stored)
            : $this->format->format($stored);
    }

    /** Rótulo de um valor do enum do campo (`label()`), do mapa de rótulos ou o próprio valor. */
    public function valueLabel(mixed $value): string
    {
        $case = $this->enumClass !== null && (is_string($value) || is_int($value))
            ? $this->enumClass::tryFrom($value)
            : null;

        if ($case !== null && method_exists($case, 'label')) {
            return (string) $case->label();
        }

        if (is_scalar($value) && isset($this->valueLabels[(string) $value])) {
            return $this->valueLabels[(string) $value];
        }

        return is_scalar($value) ? (string) $value : (string) json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    public function referencedName(mixed $id): ?string
    {
        if ($id === null) {
            return null;
        }

        return $this->referencedNames([$id])[(string) $id] ?? null;
    }

    /**
     * Nome por coluna própria ('name') ou por relação ('person.name', quando o nome mora em outra tabela).
     *
     * @param  list<int|string>  $ids
     * @return array<string, string>
     */
    public function referencedNames(array $ids): array
    {
        if ($ids === [] || $this->referencedModel === null || $this->nameColumn === null) {
            return [];
        }

        $model = new $this->referencedModel;
        $key = $model->getKeyName();
        $query = $this->referencedModel::query()
            ->withoutGlobalScopes()
            ->whereIn($key, $ids);

        if (! str_contains($this->nameColumn, '.')) {
            return $query
                ->pluck($this->nameColumn, $key)
                ->filter(fn (mixed $name): bool => $name !== null)
                ->mapWithKeys(fn (mixed $name, mixed $id): array => [(string) $id => (string) $name])
                ->all();
        }

        $relation = substr($this->nameColumn, 0, (int) strrpos($this->nameColumn, '.'));

        return $query
            ->with($relation)
            ->get()
            ->mapWithKeys(fn (Model $record): array => [(string) $record->getKey() => data_get($record, $this->nameColumn)])
            ->filter(fn (mixed $name): bool => $name !== null)
            ->map(fn (mixed $name): string => (string) $name)
            ->all();
    }
}
