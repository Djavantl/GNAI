<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Data\AccessibleEducationalMaterials;

use App\Domains\InclusiveRadar\Application\Data\Inspections\CreateInspectionData;
use App\Domains\InclusiveRadar\Domain\Enums\ConservationState;
use App\Domains\InclusiveRadar\Domain\Enums\ResourceStatus;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\WithoutValidation;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapInputName(SnakeCaseMapper::class)]
#[MergeValidationRules]
final class UpdateAccessibleEducationalMaterialData extends Data
{
    /**
     * @param  list<int>  $deficiencies
     * @param  list<int>  $accessibilityFeatures
     */
    public function __construct(
        public string $name,
        public bool $isDigital,
        public bool $isLoanable,
        public ?int $quantity,
        public ?string $assetCode,
        public ConservationState $conservationState,
        public ResourceStatus $status,
        public bool $isActive,
        public array $deficiencies,
        public CreateInspectionData $inspection,
        #[WithoutValidation]
        public array $accessibilityFeatures = [],
        public ?string $notes = null,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        $isDigital = filter_var(
            $context->fullPayload['is_digital'] ?? false,
            FILTER_VALIDATE_BOOLEAN,
        );

        return [
            'name' => ['required', 'string', 'max:255'],
            'is_digital' => ['required', 'boolean'],
            'is_loanable' => ['required', 'boolean'],
            'quantity' => [
                $isDigital ? 'nullable' : 'required',
                'integer',
                $isDigital ? 'min:0' : 'min:1',
            ],
            'asset_code' => ['nullable', 'string', 'max:50'],
            'conservation_state' => ['required'],
            'status' => ['required'],
            'is_active' => ['required', 'boolean'],
            'deficiencies' => ['required', 'array', 'min:1'],
            'deficiencies.*' => ['integer', 'exists:deficiencies,id'],
            'accessibility_features' => ['nullable', 'array'],
            'accessibility_features.*' => ['integer', 'exists:accessibility_features,id'],
            'inspection.type' => ['required'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public static function messages(): array
    {
        return [
            'name.required' => 'O nome do material pedagógico é obrigatório.',
            'quantity.required' => 'Para materiais físicos, a quantidade é obrigatória.',
            'quantity.min' => 'Para materiais físicos, a quantidade deve ser no mínimo 1.',
            'deficiencies.required' => 'Selecione pelo menos um público-alvo.',
            'deficiencies.min' => 'Selecione pelo menos um público-alvo.',
            'conservation_state.required' => 'O estado de conservação atual é obrigatório.',
            'inspection.type.required' => 'O tipo da inspeção é obrigatório.',
            'accessibility_features.*.exists' => 'Um dos recursos de acessibilidade selecionados é inválido.',
        ];
    }
}
