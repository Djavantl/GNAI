<?php

declare(strict_types=1);

namespace App\Domains\Backup\Application\Data;

use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MergeValidationRules]
final class UploadBackupData extends Data
{
    public function __construct(
        public UploadedFile $backupFile,
    ) {}

    public static function rules(): array
    {
        return [
            'backup_file' => ['required', 'file', 'max:102400', 'mimes:zip'],
        ];
    }

    public static function messages(): array
    {
        return [
            'backup_file.required' => 'Selecione um arquivo de backup.',
            'backup_file.file' => 'O backup enviado deve ser um arquivo válido.',
            'backup_file.max' => 'O backup deve possuir no máximo 100 MB.',
            'backup_file.mimes' => 'O backup deve ser um arquivo ZIP.',
        ];
    }
}
