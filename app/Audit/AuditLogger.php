<?php

namespace App\Audit;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class AuditLogger
{
    public function logRelationIfChanged(
        Model $model,
        string $field,
        array $oldIds,
        array $newIds
    ): void {
        sort($oldIds);
        sort($newIds);

        if ($oldIds === $newIds) return;

        $this->log($model, 'updated', [$field => $oldIds], [$field => $newIds]);
    }

    private function log(Model $model, string $action, array $old, array $new): void
    {
        try {
            AuditLog::create([
                'user_id'        => auth()->id(),
                'action'         => $action,
                'auditable_type' => $model->getMorphClass(),
                'auditable_id'   => $model->getKey(),
                'old_values'     => $old,
                'new_values'     => $new,
                'ip_address'     => request()?->ip(),
                'user_agent'     => request()?->userAgent(),
            ]);
        } catch (Throwable $e) {
            logger()->error('Falha ao registrar auditoria de relação', [
                'model' => get_class($model),
                'id'    => $model->getKey(),
                'error' => $e->getMessage(),
            ]);
        }
    }
}
