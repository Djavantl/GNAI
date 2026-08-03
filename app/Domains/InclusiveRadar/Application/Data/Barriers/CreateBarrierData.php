<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Data\Barriers;

use App\Domains\InclusiveRadar\Application\Data\Inspections\CreateInspectionData;
use App\Domains\InclusiveRadar\Domain\Enums\BarrierStatus;
use App\Domains\InclusiveRadar\Domain\Enums\Priority;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Validator;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MergeValidationRules]
final class CreateBarrierData extends Data
{
    /**
     * @param  list<int>  $deficiencies
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
        public BarrierStatus $status = BarrierStatus::IDENTIFIED,
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
            'status' => ['required', new Enum(BarrierStatus::class)],
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
            'status.required' => 'O status inicial é obrigatório no cadastro.',
        ];
    }

    public static function withValidator(Validator $validator): void
    {
        self::validateReporterContext($validator);
    }

    public static function validateReporterContext(Validator $validator): void
    {
        $data = $validator->getData();
        $isAnonymous = filter_var($data['is_anonymous'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $isNotApplicable = filter_var($data['not_applicable'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $hasStudent = filled($data['affected_student_id'] ?? null);
        $hasProfessional = filled($data['affected_professional_id'] ?? null);
        $hasPersonName = filled($data['affected_person_name'] ?? null);
        $hasPersonRole = filled($data['affected_person_role'] ?? null);

        $validator->after(function (Validator $validator) use (
            $isAnonymous,
            $isNotApplicable,
            $hasStudent,
            $hasProfessional,
            $hasPersonName,
            $hasPersonRole,
        ): void {
            if ($isAnonymous) {
                return;
            }

            if ($isNotApplicable) {
                if (! $hasPersonName || ! $hasPersonRole) {
                    $validator->errors()->add(
                        'affected_person_name',
                        'Para relatos gerais, informe o nome e o cargo da pessoa impactada.',
                    );
                }

                return;
            }

            if (! $hasStudent && ! $hasProfessional) {
                $message = 'É necessário informar o estudante ou profissional impactado, ou selecionar uma opção de relato (Anônimo/Geral).';
                $validator->errors()->add('affected_student_id', $message);
                $validator->errors()->add('affected_professional_id', $message);
            }
        });
    }
}
