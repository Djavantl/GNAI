<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Data\Semesters;

use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class ListSemestersData extends Data
{
    public function __construct(
        public ?int $year = null,
        public ?int $term = null,
        public ?string $label = null,
        public ?bool $isCurrent = null,
        public int $perPage = 10,
    ) {}

    public static function rules(): array
    {
        return [
            'year' => ['nullable', 'integer', 'between:1901,2155'],
            'term' => ['nullable', 'integer', Rule::in([1, 2])],
            'label' => ['nullable', 'string', 'max:255'],
            'is_current' => ['nullable', 'boolean'],
            'per_page' => ['integer', 'min:1', 'max:100'],
        ];
    }

    public static function messages(): array
    {
        return [
            'year.integer' => 'O filtro de ano deve ser um número inteiro.',
            'year.between' => 'O filtro de ano deve estar entre 1901 e 2155.',
            'term.integer' => 'O filtro de período deve ser um número inteiro.',
            'term.in' => 'O filtro de período deve ser o primeiro ou o segundo semestre.',
            'label.string' => 'O filtro de identificação deve ser um texto válido.',
            'label.max' => 'O filtro de identificação não pode ultrapassar 255 caracteres.',
            'is_current.boolean' => 'O filtro de semestre atual é inválido.',
        ];
    }
}
