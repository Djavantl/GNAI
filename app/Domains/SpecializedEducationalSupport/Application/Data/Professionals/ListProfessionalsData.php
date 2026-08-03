<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Data\Professionals;

use App\Domains\SpecializedEducationalSupport\Domain\Enums\ProfessionalStatus;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class ListProfessionalsData extends Data
{
    public function __construct(
        public ?string $name = null,
        public ?string $email = null,
        public ?string $registration = null,
        public ?int $position = null,
        public ?ProfessionalStatus $status = null,
        public int $perPage = 10,
    ) {}

    public static function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'max:255'],
            'registration' => ['nullable', 'string', 'max:50'],
            'position' => ['nullable', 'integer', Rule::exists('positions', 'id')],
            'status' => ['nullable', Rule::enum(ProfessionalStatus::class)],
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
            'registration.string' => 'O filtro de matrícula deve ser um texto válido.',
            'registration.max' => 'O filtro de matrícula não pode ultrapassar 50 caracteres.',
            'position.integer' => 'O filtro de cargo é inválido.',
            'position.exists' => 'O filtro de cargo é inválido.',
            'status.enum' => 'O filtro de status é inválido.',
        ];
    }
}
