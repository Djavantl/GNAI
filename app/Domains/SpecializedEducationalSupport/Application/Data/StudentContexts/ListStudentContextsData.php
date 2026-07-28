<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Data\StudentContexts;

use App\Domains\SpecializedEducationalSupport\Domain\Enums\ContextEvaluationType;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class ListStudentContextsData extends Data
{
    public function __construct(
        public ?int $semesterId = null,
        public ?ContextEvaluationType $evaluationType = null,
        public ?bool $isCurrent = null,
        public int $perPage = 10,
    ) {}

    public static function rules(): array
    {
        return [
            'semester_id' => ['nullable', 'integer', Rule::exists('semesters', 'id')],
            'evaluation_type' => ['nullable', Rule::enum(ContextEvaluationType::class)],
            'is_current' => ['nullable', 'boolean'],
            'per_page' => ['integer', 'min:1', 'max:100'],
        ];
    }

    public static function messages(): array
    {
        return [
            'semester_id.integer' => 'O semestre informado é inválido.',
            'semester_id.exists' => 'O semestre informado não foi encontrado.',
            'evaluation_type.enum' => 'O tipo de avaliação informado é inválido.',
            'is_current.boolean' => 'O status informado é inválido.',
            'per_page.integer' => 'A quantidade por página deve ser um número inteiro.',
            'per_page.min' => 'A quantidade por página deve ser ao menos 1.',
            'per_page.max' => 'A quantidade por página não pode ultrapassar 100.',
        ];
    }
}
