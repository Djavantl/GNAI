<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Data\Waitlists;

use App\Domains\InclusiveRadar\Domain\Enums\LoanableType;
use Illuminate\Validation\Rules\Enum;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MergeValidationRules]
final class CreateWaitlistData extends Data
{
    public function __construct(
        public int $waitlistableId,
        public LoanableType $waitlistableType,
        public ?int $studentId = null,
        public ?int $professionalId = null,
        public ?string $observation = null,
    ) {}

    public static function rules(): array
    {
        return [
            'waitlistable_id' => ['required', 'integer'],
            'waitlistable_type' => ['required', new Enum(LoanableType::class)],
            'student_id' => ['nullable', 'integer', 'exists:students,id'],
            'professional_id' => ['nullable', 'integer', 'exists:professionals,id'],
            'observation' => ['nullable', 'string'],
        ];
    }

    public static function messages(): array
    {
        return [
            'waitlistable_id.required' => 'O recurso é obrigatório.',
            'waitlistable_type.required' => 'O tipo de recurso é obrigatório.',
            'waitlistable_type.enum' => 'Tipo de recurso inválido.',
            'student_id.exists' => 'Aluno inválido.',
            'professional_id.exists' => 'Profissional inválido.',
        ];
    }
}
