<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Data\Institutions;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MergeValidationRules]
final class UpdateInstitutionData extends Data
{
    public function __construct(
        public string $name,
        public string $city,
        public string $state,
        public float $latitude,
        public float $longitude,
        public ?string $shortName = null,
        public ?string $district = null,
        public ?string $address = null,
        public ?int $defaultZoom = 16,
        public bool $isActive = false,
    ) {}

    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'short_name' => ['nullable', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:255'],
            'district' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'default_zoom' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public static function messages(): array
    {
        return [
            'name.required' => 'O nome da instituição é obrigatório.',
            'name.max' => 'O nome da instituição não pode ter mais de 255 caracteres.',
            'city.required' => 'A cidade é obrigatória.',
            'city.max' => 'O nome da cidade não pode ter mais de 255 caracteres.',
            'state.required' => 'O estado é obrigatório.',
            'state.max' => 'O nome do estado não pode ter mais de 255 caracteres.',
            'district.max' => 'O nome do distrito não pode ter mais de 255 caracteres.',
            'address.max' => 'O nome do endereço/rua não pode ter mais de 255 caracteres.',
            'latitude.required' => 'A latitude é obrigatória.',
            'latitude.numeric' => 'A latitude deve ser um número válido.',
            'latitude.between' => 'A latitude deve estar entre -90 e 90.',
            'longitude.required' => 'A longitude é obrigatória.',
            'longitude.numeric' => 'A longitude deve ser um número válido.',
            'longitude.between' => 'A longitude deve estar entre -180 e 180.',
            'default_zoom.integer' => 'O zoom padrão deve ser um número inteiro.',
            'default_zoom.min' => 'O zoom padrão não pode ser negativo.',
            'is_active.boolean' => 'O campo ativo deve ser verdadeiro ou falso.',
        ];
    }
}
