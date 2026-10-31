<?php

declare(strict_types=1);

namespace App\Domains\Settings\Application\Data;

use App\Domains\Settings\Domain\Enums\BackupRetentionMode;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class UpdateBackupSettingsData extends Data
{
    /**
     * @param  list<string>  $times
     */
    public function __construct(
        public BackupRetentionMode $retentionMode,
        public int $retentionValue,
        public array $times,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        if (array_key_exists('times', $properties) && is_array($properties['times'])) {
            $properties['times'] = array_values(array_map(
                static fn (mixed $time): mixed => is_string($time) ? trim($time) : $time,
                $properties['times'],
            ));
        }

        return $properties;
    }

    public static function rules(): array
    {
        return [
            'retention_mode' => ['required', Rule::enum(BackupRetentionMode::class)],
            'retention_value' => ['required', 'integer', 'min:1', 'max:365'],
            'times' => ['required', 'array', 'min:1', 'max:6'],
            'times.*' => ['required', 'string', 'date_format:H:i', 'distinct'],
        ];
    }

    public static function messages(): array
    {
        return [
            'retention_mode.required' => 'Informe como os backups serão mantidos.',
            'retention_mode.enum' => 'O tipo de retenção selecionado é inválido.',
            'retention_value.required' => 'Informe o valor da retenção.',
            'retention_value.integer' => 'A retenção deve ser um número inteiro.',
            'retention_value.min' => 'A retenção deve ser de pelo menos :min.',
            'retention_value.max' => 'A retenção não pode ultrapassar :max.',
            'times.required' => 'Informe pelo menos um horário de backup automático.',
            'times.array' => 'Os horários informados são inválidos.',
            'times.min' => 'Informe pelo menos um horário de backup automático.',
            'times.max' => 'Informe no máximo :max horários de backup automático.',
            'times.*.required' => 'Informe o horário.',
            'times.*.date_format' => 'Informe o horário no formato HH:MM.',
            'times.*.distinct' => 'Este horário está repetido.',
        ];
    }
}
