<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Data\Inspections;

use App\Domains\InclusiveRadar\Domain\Enums\InspectionType;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class CreateInspectionData extends Data
{
    /**
     * @param  array<int, mixed>  $images
     */
    public function __construct(
        public string $date,
        public InspectionType $type = InspectionType::INITIAL,
        public ?string $description = null,
        public array $images = [],
    ) {}

    public static function rules(): array
    {
        return [
            'date' => ['required', 'date', 'before_or_equal:today'],
            'description' => ['nullable', 'string', 'max:1000'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => [
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:5120',
            ],
        ];
    }

    public static function messages(): array
    {
        return [
            'date.required' => 'A data da inspeção é obrigatória.',
            'date.before_or_equal' => 'A data da inspeção não pode ser no futuro.',
            'images.*.image' => 'O arquivo deve ser uma imagem.',
            'images.*.mimes' => 'A imagem deve estar no formato JPEG, PNG ou WebP.',
            'images.*.max' => 'Cada imagem não pode ser maior que 5MB.',
            'images.max' => 'Uma inspeção pode possuir no máximo 10 imagens.',
        ];
    }
}
