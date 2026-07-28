<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Data\Pendencies;

use App\Enums\Priority;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class CreatePendencyData extends Data
{
    public function __construct(
        public int $assignedTo,
        public string $title,
        public Priority $priority,
        public ?string $description = null,
        public ?string $dueDate = null,
    ) {}

    public static function rules(): array
    {
        return [
            'assigned_to' => ['required', 'integer', Rule::exists('professionals', 'id')],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', Rule::enum(Priority::class)],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }

    public static function messages(): array
    {
        return [
            'assigned_to.required' => 'O profissional responsável é obrigatório.',
            'assigned_to.integer' => 'O profissional responsável informado é inválido.',
            'assigned_to.exists' => 'O profissional responsável selecionado é inválido.',
            'title.required' => 'O título da pendência é obrigatório.',
            'title.string' => 'O título da pendência deve ser um texto válido.',
            'title.max' => 'O título da pendência não pode ultrapassar 255 caracteres.',
            'description.string' => 'A descrição da pendência deve ser um texto válido.',
            'priority.required' => 'A prioridade é obrigatória.',
            'priority.enum' => 'A prioridade selecionada é inválida.',
            'due_date.date' => 'A data limite deve ser uma data válida.',
            'due_date.after_or_equal' => 'A data limite deve ser hoje ou uma data futura.',
        ];
    }
}
