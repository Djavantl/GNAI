<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Data\Deficiencies;

use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MergeValidationRules]
final class CreateDeficiencyData extends Data
{
    public function __construct(
        public string $name,
        public ?string $cidCode = null,
        public ?string $description = null,
        public bool $isActive = true,
    ) {}

    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'cid_code' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('deficiencies', 'cid_code'),
            ],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public static function messages(): array
    {
        return [
            'name.required' => 'O nome do perfil de atendimento é obrigatório.',
            'name.string' => 'O nome do perfil de atendimento deve ser um texto válido.',
            'name.max' => 'O nome não pode ultrapassar 255 caracteres.',
            'cid_code.string' => 'O código CID deve ser um texto válido.',
            'cid_code.unique' => 'O código CID informado já está cadastrado.',
            'cid_code.max' => 'O código CID deve ter no máximo 20 caracteres.',
            'description.string' => 'A descrição deve ser um texto válido.',
            'is_active.boolean' => 'O status deve ser verdadeiro ou falso.',
        ];
    }
}
