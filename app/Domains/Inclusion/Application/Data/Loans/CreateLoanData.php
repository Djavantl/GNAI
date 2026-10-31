<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Data\Loans;

use App\Domains\Inclusion\Domain\Enums\ItemType;
use Illuminate\Validation\Rules\Enum;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MergeValidationRules]
final class CreateLoanData extends Data
{
    public function __construct(
        public int $loanableId,
        public ItemType $itemType,
        public string $loanDate,
        public string $dueDate,
        public ?int $studentId = null,
        public ?int $professionalId = null,
        public ?string $observation = null,
    ) {}

    public static function rules(): array
    {
        return [
            'loanable_id' => ['required', 'integer'],
            'item_type' => [
                'required',
                new Enum(ItemType::class),
            ],
            'student_id' => ['nullable', 'integer', 'exists:students,id'],
            'professional_id' => ['nullable', 'integer', 'exists:professionals,id'],
            'loan_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:loan_date'],
            'observation' => ['nullable', 'string'],
        ];
    }

    public static function messages(): array
    {
        return [
            'loanable_id.required' => 'O item para empréstimo não foi identificado.',
            'item_type.required' => 'O tipo de item é obrigatório.',
            'item_type.enum' => 'O tipo de item selecionado é inválido.',
            'due_date.required' => 'A data de previsão de entrega é obrigatória.',
            'due_date.after_or_equal' => 'A data de entrega não pode ser anterior à data do empréstimo.',
        ];
    }
}
