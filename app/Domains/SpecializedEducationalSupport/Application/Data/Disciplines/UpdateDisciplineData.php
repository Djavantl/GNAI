<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Data\Disciplines;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MergeValidationRules]
final class UpdateDisciplineData extends Data
{
    public function __construct(
        public string $name,
        public bool $isActive,
        public ?string $description = null,
    ) {}

    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public static function messages(): array
    {
        return [
            'name.required' => 'O nome da disciplina é obrigatório.',
            'name.string' => 'O nome da disciplina deve ser um texto válido.',
            'name.max' => 'O nome da disciplina não pode ultrapassar 255 caracteres.',
            'description.string' => 'A descrição da disciplina deve ser um texto válido.',
            'is_active.required' => 'Selecione a situação da disciplina.',
            'is_active.boolean' => 'O campo de situação da disciplina é inválido.',
        ];
    }
}
