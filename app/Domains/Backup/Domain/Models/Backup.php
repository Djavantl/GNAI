<?php

declare(strict_types=1);

namespace App\Domains\Backup\Domain\Models;

use App\Domains\Audit\Domain\Contracts\Auditable;
use App\Domains\Audit\Domain\ValueObjects\AuditedField;
use App\Domains\Audit\Infrastructure\Eloquent\AuditsChanges;
use App\Domains\Auth\Domain\Models\User;
use App\Domains\Backup\Domain\DTOs\CreateBackupDTO;
use App\Domains\Backup\Domain\Enums\BackupStatus;
use App\Domains\Backup\Domain\Exceptions\InvalidBackup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Backup extends Model implements Auditable
{
    use AuditsChanges;

    protected $fillable = [
        'file_name',
        'file_path',
        'size',
        'status',
        'user_id',
    ];

    protected $casts = [
        'status' => BackupStatus::class,
    ];

    public static function auditType(): string
    {
        return 'backup';
    }

    public static function auditLabel(): string
    {
        return 'Backup';
    }

    public static function auditedFields(): array
    {
        return [
            'file_name' => AuditedField::text('Arquivo'),
            'file_path' => AuditedField::file('Caminho do arquivo'),
            'size' => AuditedField::text('Tamanho'),
            'status' => AuditedField::enum('Status', BackupStatus::class),
            'user_id' => AuditedField::reference('Gerado por', User::class),
        ];
    }

    public function auditDescription(): string
    {
        return "o backup {$this->file_name}";
    }

    public static function register(CreateBackupDTO $data): self
    {
        return new self([
            'file_name' => trim($data->fileName),
            'file_path' => trim($data->filePath),
            'size' => trim($data->size),
            'status' => $data->status,
            'user_id' => $data->userId,
        ]);
    }

    /**
     * @throws InvalidBackup
     */
    public function ensureCanBeRestored(): void
    {
        if (! $this->exists) {
            throw new InvalidBackup('O backup precisa estar persistido antes da restauração.');
        }

        if ($this->status?->allowsRestore() !== true) {
            throw new InvalidBackup('Apenas backups concluídos com sucesso podem ser restaurados.');
        }

        if (blank($this->file_name) || blank($this->file_path)) {
            throw new InvalidBackup('O backup selecionado não possui referência válida para o arquivo físico.');
        }
    }

    /**
     * @throws InvalidBackup
     */
    public function ensureCanBeDeleted(): void
    {
        if (! $this->exists) {
            throw new InvalidBackup('O backup precisa estar persistido antes da exclusão.');
        }

        if (blank($this->file_path)) {
            throw new InvalidBackup('O backup selecionado não possui referência válida para o arquivo físico.');
        }
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
