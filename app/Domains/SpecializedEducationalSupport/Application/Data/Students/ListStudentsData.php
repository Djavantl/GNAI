<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Data\Students;

use App\Domains\SpecializedEducationalSupport\Domain\Enums\StudentStatus;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class ListStudentsData extends Data
{
    public function __construct(
        public ?string $name = null,
        public ?string $phone = null,
        public ?string $email = null,
        public ?string $registration = null,
        public ?StudentStatus $status = null,
        public int $perPage = 10,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        if (array_key_exists('phone', $properties)) {
            $digits = preg_replace('/\D/', '', (string) $properties['phone']) ?? '';
            $properties['phone'] = $digits === '' ? null : $digits;
        }

        return $properties;
    }

    public static function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:11'],
            'email' => ['nullable', 'string', 'max:255'],
            'registration' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', Rule::enum(StudentStatus::class)],
            'per_page' => ['integer', 'min:1', 'max:100'],
        ];
    }

    public static function messages(): array
    {
        return [
            'name.string' => 'O filtro de nome deve ser um texto válido.',
            'name.max' => 'O filtro de nome não pode ultrapassar 255 caracteres.',
            'phone.string' => 'O filtro de telefone deve ser um texto válido.',
            'phone.max' => 'O filtro de telefone não pode ultrapassar 11 dígitos.',
            'email.string' => 'O filtro de e-mail deve ser um texto válido.',
            'email.max' => 'O filtro de e-mail não pode ultrapassar 255 caracteres.',
            'registration.string' => 'O filtro de matrícula deve ser um texto válido.',
            'registration.max' => 'O filtro de matrícula não pode ultrapassar 50 caracteres.',
            'status.enum' => 'O filtro de status é inválido.',
        ];
    }
}
