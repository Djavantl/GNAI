<?php

declare(strict_types=1);

namespace App\Domains\Backup\Application\Data;

use App\Domains\Backup\Domain\Enums\BackupStatus;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class ListBackupsData extends Data
{
    public function __construct(
        public ?string $name = null,
        public ?BackupStatus $status = null,
        public ?int $userId = null,
        public int $perPage = 10,
    ) {}

    public static function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::enum(BackupStatus::class)],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'per_page' => ['integer', 'min:1', 'max:100'],
        ];
    }
}
