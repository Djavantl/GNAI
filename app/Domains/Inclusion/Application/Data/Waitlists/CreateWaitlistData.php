<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Data\Waitlists;

use App\Domains\Inclusion\Domain\Enums\ItemType;
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
        public ItemType $itemType,
        public ?int $studentId = null,
        public ?int $professionalId = null,
        public ?string $observation = null,
    ) {}

    public static function rules(): array
    {
        return [
            'waitlistable_id' => ['required', 'integer'],
            'item_type' => ['required', new Enum(ItemType::class)],
            'student_id' => ['nullable', 'integer', 'exists:students,id'],
            'professional_id' => ['nullable', 'integer', 'exists:professionals,id'],
            'observation' => ['nullable', 'string'],
        ];
    }

    public static function messages(): array
    {
        return [
            'waitlistable_id.required' => 'O recurso é obrigatório.',
            'item_type.required' => 'O tipo de recurso é obrigatório.',
            'item_type.enum' => 'Tipo de recurso inválido.',
            'student_id.exists' => 'Aluno inválido.',
            'professional_id.exists' => 'Profissional inválido.',
        ];
    }
}
