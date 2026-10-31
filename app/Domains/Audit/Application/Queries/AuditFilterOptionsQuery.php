<?php

declare(strict_types=1);

namespace App\Domains\Audit\Application\Queries;

use App\Domains\Audit\Application\Data\ListAuditRecordsData;
use App\Domains\Audit\Domain\Enums\AuditEvent;
use App\Domains\Audit\Domain\Models\AuditRecord;
use App\Domains\Audit\Infrastructure\AuditableEntities;

/** Opções dos filtros: autores que aparecem no histórico, tipos auditáveis e eventos. */
final class AuditFilterOptionsQuery
{
    public function __construct(private readonly AuditableEntities $entities) {}

    /**
     * @return array{
     *     actors: list<array{value: string, label: string}>,
     *     types: list<array{value: string, label: string}>,
     *     events: list<array{value: string, label: string}>,
     * }
     */
    public function execute(): array
    {
        // O nome mais recente de cada autor que já aparece no histórico.
        $actors = AuditRecord::query()
            ->whereNotNull('actor_id')
            ->selectRaw('actor_id, MAX(actor_name) AS name')
            ->groupBy('actor_id')
            ->orderBy('name')
            ->toBase()
            ->get()
            ->map(fn (object $row): array => ['value' => (string) $row->actor_id, 'label' => (string) $row->name])
            ->all();

        return [
            'actors' => [['value' => ListAuditRecordsData::SYSTEM_ACTOR, 'label' => 'Sistema (sem usuário)'], ...$actors],
            'types' => $this->entities->options(),
            'events' => AuditEvent::options(),
        ];
    }
}
