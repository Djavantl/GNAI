<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Data\Courses;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MergeValidationRules]
final class CreateCourseData extends Data
{
    /**
     * @param list<int> $disciplineIds
     */
    public function __construct(
        public string $name,
        public ?string $description = null,
        public array $disciplineIds = [],
    ) {}

    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'discipline_ids' => ['nullable', 'array'],
            'discipline_ids.*' => ['integer', 'exists:disciplines,id'],
        ];
    }

    public static function messages(): array
    {
        return [
            'name.required' => 'O nome do curso é obrigatório.',
            'name.string' => 'O nome do curso deve ser um texto válido.',
            'name.max' => 'O nome do curso não pode ultrapassar 255 caracteres.',
            'description.string' => 'A descrição do curso deve ser um texto válido.',
            'discipline_ids.array' => 'As disciplinas do curso devem ser informadas em uma lista válida.',
            'discipline_ids.*.integer' => 'Uma das disciplinas selecionadas é inválida.',
            'discipline_ids.*.exists' => 'Uma das disciplinas selecionadas é inválida.',
        ];
    }
}
