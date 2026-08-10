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
     * @param  array<int, mixed>  $evidences
     */
    public function __construct(
        public string $date,
        public InspectionType $type = InspectionType::INITIAL,
        public ?string $description = null,
        public array $evidences = [],
    ) {}

    public static function rules(): array
    {
        return [
            'date' => ['required', 'date', 'before_or_equal:today'],
            'description' => ['nullable', 'string', 'max:1000'],
            'evidences' => ['nullable', 'array', 'max:10'],
            'evidences.*' => [
                'file',
                'mimes:jpeg,png,jpg,webp,pdf,doc,docx,ppt,pptx,odp,odt,ods,xls,xlsx,csv,txt',
                'max:20480',
            ],
        ];
    }

    public static function messages(): array
    {
        return [
            'date.required' => 'A data da inspeção é obrigatória.',
            'date.before_or_equal' => 'A data da inspeção não pode ser no futuro.',
            'evidences.*.file' => 'A evidência deve ser um arquivo válido.',
            'evidences.*.mimes' => 'A evidência deve estar em um dos formatos permitidos: JPG, PNG, WebP, PDF, DOC, DOCX, PPT, PPTX, ODP, ODT, ODS, XLS, XLSX, CSV ou TXT.',
            'evidences.*.max' => 'Cada evidência não pode ser maior que 20MB.',
            'evidences.max' => 'Uma inspeção pode possuir no máximo 10 evidências.',
        ];
    }
}
