<?php

declare(strict_types=1);

namespace App\Domains\Audit\Application\Actions;

use App\Domains\Audit\Domain\Contracts\Auditable;
use App\Domains\Audit\Domain\Enums\AuditEvent;
use App\Domains\Audit\Domain\ValueObjects\AuditChanges;
use Illuminate\Database\Eloquent\Model;

/**
 * Registra a mudança de um campo que não passa pelos eventos do Eloquent: pivôs (permissões do cargo,
 * disciplinas do curso) e escritas em massa. Funde com o registro da mesma entidade na operação: "criou o
 * cargo X" já traz as permissões.
 */
final class RecordAuditFieldChangeAction
{
    public function __construct(private readonly RecordAuditChangeAction $record) {}

    public function execute(Model&Auditable $entity, string $field, mixed $before, mixed $after): void
    {
        $this->record->execute(
            $entity,
            AuditEvent::UPDATED,
            AuditChanges::forField($entity, $field, $before, $after),
        );
    }
}
