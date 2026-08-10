<?php

declare(strict_types=1);

namespace App\Domains\Backup\Domain\Models;

use App\Domains\Backup\Domain\DTOs\CreateBackupDTO;
use App\Domains\Backup\Domain\Enums\BackupStatus;
use App\Domains\Backup\Domain\Exceptions\InvalidBackup;
use App\Domains\Auth\Domain\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Backup extends Model
{
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
