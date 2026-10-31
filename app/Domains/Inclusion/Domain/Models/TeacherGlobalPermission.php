<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\Models;

use App\Domains\Audit\Domain\Contracts\Auditable;
use App\Domains\Audit\Domain\ValueObjects\AuditedField;
use App\Domains\Audit\Infrastructure\Eloquent\AuditsChanges;
use App\Domains\Auth\Domain\Models\Permission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Permissão concedida a todos os professores. */
final class TeacherGlobalPermission extends Model implements Auditable
{
    use AuditsChanges;

    protected $table = 'teacher_global_permissions';

    protected $fillable = [
        'permission_id',
    ];

    public static function auditType(): string
    {
        return 'teacher_global_permission';
    }

    public static function auditLabel(): string
    {
        return 'Permissão Global de Professores';
    }

    public static function auditedFields(): array
    {
        return [
            'permission_id' => AuditedField::reference('Permissão', Permission::class),
        ];
    }

    public function auditDescription(): string
    {
        return 'a permissão global de professores '.($this->permission?->name ?? "#{$this->permission_id}");
    }

    /**
     * Concede e revoga registro a registro para cada alteração entrar na auditoria.
     *
     * @param  list<int|string>  $permissionIds
     */
    public static function syncPermissions(array $permissionIds): void
    {
        $permissionIds = array_values(array_unique(array_map('intval', $permissionIds)));

        self::query()
            ->whereNotIn('permission_id', $permissionIds)
            ->get()
            ->each(fn (self $permission) => $permission->delete());

        $currentPermissionIds = self::query()
            ->pluck('permission_id')
            ->map(fn (mixed $id): int => (int) $id)
            ->all();

        foreach (array_diff($permissionIds, $currentPermissionIds) as $permissionId) {
            self::query()->create(['permission_id' => $permissionId]);
        }
    }

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }
}
