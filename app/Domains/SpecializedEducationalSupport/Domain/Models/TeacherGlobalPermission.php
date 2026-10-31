<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\Models;

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

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }
}
