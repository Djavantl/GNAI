<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Data\StudentDocuments;

use App\Domains\SpecializedEducationalSupport\Domain\Enums\StudentDocumentType;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class ListStudentDocumentsData extends Data
{
    public function __construct(
        public ?string $title = null,
        public ?StudentDocumentType $type = null,
        public ?int $semesterId = null,
        public int $perPage = 10,
    ) {}

    public static function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'type' => ['nullable', Rule::enum(StudentDocumentType::class)],
            'semester_id' => ['nullable', 'integer', 'exists:semesters,id'],
            'per_page' => ['integer', 'min:1', 'max:100'],
        ];
    }

    public static function messages(): array
    {
        return [
            'title.string' => 'O filtro de título deve ser um texto válido.',
            'title.max' => 'O filtro de título não pode ultrapassar 255 caracteres.',
            'type.enum' => 'O filtro de tipo de documento é inválido.',
            'semester_id.integer' => 'O semestre informado é inválido.',
            'semester_id.exists' => 'O semestre informado não foi encontrado.',
            'per_page.integer' => 'A quantidade por página deve ser um número inteiro.',
            'per_page.min' => 'A quantidade por página deve ser ao menos 1.',
            'per_page.max' => 'A quantidade por página não pode ultrapassar 100.',
        ];
    }
}
