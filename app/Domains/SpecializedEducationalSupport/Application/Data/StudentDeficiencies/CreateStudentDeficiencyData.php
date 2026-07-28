<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Data\StudentDeficiencies;

use App\Domains\SpecializedEducationalSupport\Domain\Enums\DeficiencySeverity;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class CreateStudentDeficiencyData extends Data
{
    public function __construct(
        public int $deficiencyId,
        public ?DeficiencySeverity $severity = null,
        public ?string $notes = null,
    ) {}

    public static function rules(): array
    {
        $student = request()->route('student');
        $studentId = $student instanceof Student ? (int) $student->getKey() : $student;

        return [
            'deficiency_id' => [
                'required',
                'integer',
                Rule::exists('deficiencies', 'id'),
                Rule::unique('students_deficiencies', 'deficiency_id')
                    ->where('student_id', $studentId),
            ],
            'severity' => ['nullable', Rule::enum(DeficiencySeverity::class)],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public static function messages(): array
    {
        return [
            'deficiency_id.required' => 'O perfil de atendimento é obrigatório.',
            'deficiency_id.integer' => 'O perfil de atendimento selecionado é inválido.',
            'deficiency_id.exists' => 'O perfil de atendimento selecionado é inválido.',
            'deficiency_id.unique' => 'Este aluno já possui o perfil de atendimento selecionado.',
            'severity.enum' => 'A severidade selecionada é inválida.',
            'notes.string' => 'As observações devem ser informadas em um texto válido.',
            'notes.max' => 'As observações não podem ultrapassar 1000 caracteres.',
        ];
    }
}
