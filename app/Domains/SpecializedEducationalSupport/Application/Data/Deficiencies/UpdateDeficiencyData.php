<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Data\Deficiencies;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\References\RouteParameterReference;

#[MapInputName(SnakeCaseMapper::class)]
#[MergeValidationRules]
final class UpdateDeficiencyData extends Data
{
    public function __construct(
        public string $name,
        #[Unique('deficiencies', 'cid_code', ignore: new RouteParameterReference('deficiency', 'id'))]
        public ?string $cidCode = null,
        public ?string $description = null,
    ) {}

    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'cid_code' => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string'],
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
        ];
    }
}
