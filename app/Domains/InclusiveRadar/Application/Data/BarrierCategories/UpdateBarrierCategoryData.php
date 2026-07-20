<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Data\BarrierCategories;

use App\Domains\InclusiveRadar\Domain\Models\BarrierCategory;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MergeValidationRules]
final class UpdateBarrierCategoryData extends Data
{
    public function __construct(
        public string $name,
        public ?string $description = null,
        public bool $blocksMap = false,
        public bool $isActive = false,
    ) {}

    public static function rules(): array
    {
        $category = request()->route('barrierCategory');
        $categoryId = $category instanceof BarrierCategory
            ? $category->id
            : null;

        return [
            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('barrier_categories', 'name')
                    ->ignore($categoryId)
                    ->whereNull('deleted_at'),
            ],
            'description' => ['nullable', 'string'],
            'blocks_map' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public static function messages(): array
    {
        return [
            'name.required' => 'O nome da categoria é obrigatório.',
            'name.unique' => 'O nome da categoria já está em uso.',
            'blocks_map.boolean' => 'O campo de bloquear mapa deve ser verdadeiro ou falso.',
            'is_active.boolean' => 'O campo ativo deve ser verdadeiro ou falso.',
        ];
    }
}
