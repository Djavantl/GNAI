<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Data\Locations;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MergeValidationRules]
final class UpdateLocationData extends Data
{
    public function __construct(
        public int $institutionId,
        public string $name,
        public float $latitude,
        public float $longitude,
        public ?string $type = null,
        public ?string $description = null,
        public ?string $googlePlaceId = null,
        public bool $isActive = false,
    ) {}

    public static function rules(): array
    {
        return [
            'institution_id' => ['required', 'exists:institutions,id'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'google_place_id' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public static function messages(): array
    {
        return [
            'institution_id.required' => 'A instituição é obrigatória.',
            'institution_id.exists' => 'A instituição selecionada não existe.',
            'name.required' => 'O nome do local é obrigatório.',
            'name.max' => 'O nome do local não pode ter mais de 255 caracteres.',
            'type.max' => 'O tipo do local não pode ter mais de 100 caracteres.',
            'description.max' => 'A descrição deve possuir no máximo 1000 caracteres.',
            'latitude.required' => 'A latitude é obrigatória.',
            'latitude.numeric' => 'A latitude deve ser um número válido.',
            'latitude.between' => 'A latitude deve estar entre -90 e 90.',
            'longitude.required' => 'A longitude é obrigatória.',
            'longitude.numeric' => 'A longitude deve ser um número válido.',
            'longitude.between' => 'A longitude deve estar entre -180 e 180.',
            'google_place_id.max' => 'O ID do Google Place não pode ter mais de 255 caracteres.',
            'is_active.boolean' => 'O campo ativo deve ser verdadeiro ou falso.',
        ];
    }
}
