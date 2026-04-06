<?php

namespace App\Models\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

trait Auditable
{
    private const AUDIT_EXCLUDED_FIELDS = ['created_at', 'updated_at', 'deleted_at'];

    protected static function bootAuditable(): void
    {
        static::created(function (Model $model) {
            self::writeAudit('created', $model, [], self::filterAuditFields($model, $model->getAttributes()));
        });

        static::updating(function (Model $model) {
            $dirty = array_diff_key(
                $model->getDirty(),
                array_flip(self::AUDIT_EXCLUDED_FIELDS)
            );

            $dirty = array_diff_key($dirty, array_flip(
                property_exists($model, 'auditExclude') ? $model->auditExclude : []
            ));

            if (empty($dirty)) return;

            self::writeAudit(
                'updated',
                $model,
                array_intersect_key($model->getOriginal(), $dirty),
                $dirty
            );
        });

        static::deleting(function (Model $model) {
            self::writeAudit('deleted', $model, self::filterAuditFields($model, $model->getAttributes()), []);
        });
    }

    protected static function filterAuditFields(Model $model, array $attributes): array
    {
        $exclude = array_merge(
            self::AUDIT_EXCLUDED_FIELDS,
            $model->getHidden(),
            property_exists($model, 'auditExclude') ? $model->auditExclude : []
        );

        return array_diff_key($attributes, array_flip($exclude));
    }

    protected static function writeAudit(string $action, Model $model, array $old, array $new): void
    {
        try {
            AuditLog::create([
                'user_id'        => auth()->id(),
                'action'         => $action,
                'auditable_type' => $model->getMorphClass(),
                'auditable_id'   => $model->getKey(),
                'old_values'     => !empty($old) ? $old : null,
                'new_values'     => !empty($new) ? $new : null,
                'ip_address'     => request()?->ip(),
                'user_agent'     => request()?->userAgent(),
            ]);
        } catch (\Throwable $e) {
            logger()->error('Falha ao registrar auditoria', [
                'action' => $action,
                'model'  => get_class($model),
                'id'     => $model->getKey(),
                'error'  => $e->getMessage(),
            ]);
        }
    }
}
