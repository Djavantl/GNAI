<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Data\Tags;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\References\RouteParameterReference;

#[MapInputName(SnakeCaseMapper::class)]
#[MergeValidationRules]
final class UpdateTagData extends Data
{
    public function __construct(
        #[Unique('tags', ignore: new RouteParameterReference('tag', 'id'))]
        public string $name,
        public bool $isActive = false,
    ) {}

    public static function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
            ],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public static function messages(): array
    {
        return [
            'name.required' => 'O nome da etiqueta é obrigatório.',
            'name.max' => 'O nome da etiqueta deve possuir no máximo 100 caracteres.',
            'name.unique' => 'Essa etiqueta já existe.',
            'is_active.boolean' => 'O status deve ser verdadeiro ou falso.',
        ];
    }
}
