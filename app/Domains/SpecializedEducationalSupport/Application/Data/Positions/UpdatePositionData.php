<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Data\Positions;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class UpdatePositionData extends Data
{
    /**
     * @param list<int> $permissions
     */
    public function __construct(
        public string $name,
        public ?string $description = null,
        public array $permissions = [],
    ) {}

    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ];
    }

    public static function messages(): array
    {
        return [
            'name.required' => 'O nome do cargo é obrigatório.',
            'name.string' => 'O nome do cargo deve ser um texto válido.',
            'name.max' => 'O nome não pode ultrapassar 255 caracteres.',
            'description.string' => 'A descrição do cargo deve ser um texto válido.',
            'permissions.array' => 'As permissões devem ser informadas em uma lista válida.',
            'permissions.*.integer' => 'Uma das permissões selecionadas é inválida.',
            'permissions.*.exists' => 'Uma das permissões selecionadas é inválida.',
        ];
    }
}
