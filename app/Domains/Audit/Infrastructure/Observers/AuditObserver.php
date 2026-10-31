<?php

declare(strict_types=1);

namespace App\Domains\Audit\Infrastructure\Observers;

use App\Domains\Audit\Application\Actions\RecordAuditChangeAction;
use App\Domains\Audit\Domain\Contracts\Auditable;
use App\Domains\Audit\Domain\Enums\AuditEvent;
use App\Domains\Audit\Domain\ValueObjects\AuditChanges;
use Illuminate\Database\Eloquent\Model;

/** Toda criação, alteração e exclusão de um model auditável entra no histórico do sistema. */
final class AuditObserver
{
    public function __construct(private readonly RecordAuditChangeAction $record) {}

    public function created(Model&Auditable $entity): void
    {
        $this->record->execute($entity, AuditEvent::CREATED, AuditChanges::fromCreation($entity));
    }

    public function updated(Model&Auditable $entity): void
    {
        $this->record->execute($entity, AuditEvent::UPDATED, AuditChanges::fromUpdate($entity));
    }

    public function deleting(Model&Auditable $entity): void
    {
        $entity->snapshotAuditExtraValues();
    }

    public function deleted(Model&Auditable $entity): void
    {
        $this->record->execute($entity, AuditEvent::DELETED, AuditChanges::fromDeletion($entity));
    }

    /** Soft delete desfeito: a foto dos campos, como na criação. */
    public function restored(Model&Auditable $entity): void
    {
        $this->record->execute($entity, AuditEvent::RESTORED, AuditChanges::fromCreation($entity));
    }
}
