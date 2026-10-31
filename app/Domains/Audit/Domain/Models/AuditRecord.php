<?php

declare(strict_types=1);

namespace App\Domains\Audit\Domain\Models;

use App\Domains\Audit\Domain\Enums\AuditEvent;
use App\Domains\Audit\Domain\Enums\FieldFormat;
use App\Domains\Audit\Domain\ValueObjects\AuditChanges;
use App\Domains\Auth\Domain\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Uma linha do histórico: um registro criado, alterado ou excluído numa operação.
 * Gravado pela RecordAuditChangeAction e nunca editado fora da fusão da própria operação.
 *
 * @property int $id
 * @property string $operation
 * @property int|null $actor_id
 * @property string|null $actor_name
 * @property int|null $impersonator_id administrador que agiu como o ator, pelo "entrar como"
 * @property string|null $impersonator_name
 * @property AuditEvent $event
 * @property string $entity_type
 * @property string $entity_id
 * @property string $entity_description
 * @property array<string, array{before: mixed, after: mixed}> $changes
 * @property array<string, mixed>|null $referenced_names
 * @property array<string, mixed>|null $context
 * @property CarbonImmutable|null $created_at
 */
final class AuditRecord extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'audit_records';

    protected $fillable = [
        'operation',
        'actor_id',
        'actor_name',
        'impersonator_id',
        'impersonator_name',
        'event',
        'entity_type',
        'entity_id',
        'entity_description',
        'changes',
        'referenced_names',
        'context',
    ];

    protected $casts = [
        'event' => AuditEvent::class,
        'changes' => 'array',
        'referenced_names' => 'array',
        'context' => 'array',
        'created_at' => 'immutable_datetime',
    ];

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    /** "Maria", "Sistema" (sem usuário) ou "Admin em nome de Maria" no "entrar como". */
    public function author(): string
    {
        if ($this->impersonator_name !== null) {
            return "{$this->impersonator_name} em nome de ".($this->actor_name ?? 'um usuário excluído');
        }

        return $this->actor_name ?? 'Sistema';
    }

    /**
     * Junta uma mudança posterior do mesmo registro, na mesma operação: o `before` continua o do início da
     * operação e o `after` passa a ser o mais recente. Campo que voltou ao valor inicial sai; o sigiloso fica.
     *
     * @param  array<string, mixed>  $referencedNames
     */
    public function merge(AuditChanges $new, array $referencedNames, string $currentDescription): void
    {
        $changes = $this->changes;
        $names = $this->referenced_names ?? [];

        foreach ($new->byField as $field => $change) {
            $beforeOperation = array_key_exists($field, $changes) ? $changes[$field]['before'] : $change['before'];

            if ($beforeOperation === $change['after'] && $beforeOperation !== FieldFormat::HIDDEN_VALUE) {
                unset($changes[$field], $names[$field]);

                continue;
            }

            $changes[$field] = ['before' => $beforeOperation, 'after' => $change['after']];

            if (isset($referencedNames[$field])) {
                $names[$field] = self::mergeNames($names[$field] ?? null, $referencedNames[$field]);
            }
        }

        $this->changes = $changes;
        $this->referenced_names = $names ?: null;
        $this->entity_description = $currentDescription;
    }

    public function hasNoChanges(): bool
    {
        return $this->changes === [];
    }

    /** A descrição cabe na coluna (255). */
    protected function entityDescription(): Attribute
    {
        return Attribute::set(fn (string $description) => mb_substr($description, 0, 255));
    }

    /**
     * Referência: {before, after}; o `before` do início da operação prevalece. Lista: {items: {id: nome}}, somados.
     *
     * @param  array<string, mixed>|null  $current
     * @param  array<string, mixed>  $new
     * @return array<string, mixed>
     */
    private static function mergeNames(?array $current, array $new): array
    {
        if ($current === null) {
            return $new;
        }

        if (isset($new['items'])) {
            return ['items' => ($current['items'] ?? []) + $new['items']];
        }

        return [
            'before' => array_key_exists('before', $current) ? $current['before'] : ($new['before'] ?? null),
            'after' => $new['after'] ?? null,
        ];
    }
}
