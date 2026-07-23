<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Data\Barriers;

use App\Domains\InclusiveRadar\Application\Data\Inspections\CreateInspectionData;
use App\Domains\InclusiveRadar\Domain\Enums\BarrierStatus;
use App\Enums\Priority;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Validator;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MergeValidationRules]
final class UpdateBarrierData extends Data
{
    /**
     * @param list<int> $deficiencies
     */
    public function __construct(
        public string $name,
        public int $institutionId,
        public int $barrierCategoryId,
        public Priority $priority,
        public string $identifiedAt,
        public array $deficiencies,
        public CreateInspectionData $inspection,
        public ?string $description = null,
        public ?int $locationId = null,
        public ?float $latitude = null,
        public ?float $longitude = null,
        public ?string $locationSpecificDetails = null,
        public bool $noLocation = false,
        public bool $isAnonymous = false,
        public bool $notApplicable = false,
        public ?int $affectedStudentId = null,
        public ?int $affectedProfessionalId = null,
        public ?string $affectedPersonName = null,
        public ?string $affectedPersonRole = null,
        public ?BarrierStatus $status = null,
        public bool $isActive = true,
    ) {}

    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'institution_id' => ['required', 'exists:institutions,id'],
            'barrier_category_id' => ['required', 'exists:barrier_categories,id'],
            'priority' => ['required', new Enum(Priority::class)],
            'location_id' => ['nullable', 'exists:locations,id'],
            'no_location' => ['sometimes', 'boolean'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'location_specific_details' => ['nullable', 'string', 'max:255'],
            'is_anonymous' => ['sometimes', 'boolean'],
            'not_applicable' => ['sometimes', 'boolean'],
            'affected_student_id' => ['nullable', 'exists:students,id'],
            'affected_professional_id' => ['nullable', 'exists:professionals,id'],
            'affected_person_name' => ['nullable', 'string', 'max:255'],
            'affected_person_role' => ['nullable', 'string', 'max:255'],
            'identified_at' => ['required', 'date'],
            'deficiencies' => ['required', 'array', 'min:1'],
            'deficiencies.*' => ['exists:deficiencies,id'],
            'status' => ['nullable', new Enum(BarrierStatus::class)],
            'inspection.type' => ['required'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public static function messages(): array
    {
        return [
            'name.required' => 'O nome da barreira é obrigatório.',
            'institution_id.required' => 'A instituição é obrigatória.',
            'barrier_category_id.required' => 'A categoria da barreira é obrigatória.',
            'identified_at.required' => 'A data de identificação é obrigatória.',
            'deficiencies.required' => 'Selecione pelo menos um público afetado.',
            'deficiencies.min' => 'Selecione pelo menos um público afetado.',
            'inspection.type.required' => 'O tipo da vistoria é obrigatório.',
        ];
    }

    public static function withValidator(Validator $validator): void
    {
        CreateBarrierData::validateReporterContext($validator);
    }
}
