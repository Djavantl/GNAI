<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Data\Guardians;

use App\Domains\SpecializedEducationalSupport\Domain\Enums\GuardianRelationship;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class ListGuardiansData extends Data
{
    public function __construct(
        public ?string $name = null,
        public ?string $email = null,
        public ?GuardianRelationship $relationship = null,
        public int $perPage = 10,
    ) {}

    public static function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'max:255'],
            'relationship' => ['nullable', Rule::enum(GuardianRelationship::class)],
            'per_page' => ['integer', 'min:1', 'max:100'],
        ];
    }

    public static function messages(): array
    {
        return [
            'name.string' => 'O filtro de nome deve ser um texto válido.',
            'name.max' => 'O filtro de nome não pode ultrapassar 255 caracteres.',
            'email.string' => 'O filtro de e-mail deve ser um texto válido.',
            'email.max' => 'O filtro de e-mail não pode ultrapassar 255 caracteres.',
            'relationship.enum' => 'O filtro de parentesco é inválido.',
        ];
    }
}
