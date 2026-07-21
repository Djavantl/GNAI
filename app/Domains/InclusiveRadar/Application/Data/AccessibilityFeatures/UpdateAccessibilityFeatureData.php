<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Data\AccessibilityFeatures;

use App\Domains\InclusiveRadar\Domain\Models\AccessibilityFeature;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MergeValidationRules]
final class UpdateAccessibilityFeatureData extends Data
{
    public function __construct(
        public string $name,
        public ?string $description = null,
        public bool $isActive = false,
    ) {}

    public static function rules(): array
    {
        $feature = request()->route('accessibilityFeature');
        $featureId = $feature instanceof AccessibilityFeature
            ? $feature->id
            : null;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('accessibility_features', 'name')->ignore($featureId),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public static function messages(): array
    {
        return [
            'name.required' => 'O nome do recurso é obrigatório.',
            'name.unique' => 'Esse recurso já existe.',
            'description.string' => 'A descrição deve ser um texto.',
            'description.max' => 'A descrição deve possuir no máximo 1000 caracteres.',
            'is_active.boolean' => 'O status deve ser verdadeiro ou falso.',
        ];
    }
}
