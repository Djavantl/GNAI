<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Data\StudentCourses;

use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class CreateStudentCourseData extends Data
{
    public function __construct(
        public int $courseId,
        public int $academicYear,
        public bool $isCurrent = false,
        public ?string $schoolAttendanceStatus = null,
        public array $failedDisciplineIds = [],
        public array $atRiskDisciplineIds = [],
    ) {}

    public static function rules(): array
    {
        $student = request()->route('student');
        $studentId = $student instanceof Student ? (int) $student->getKey() : $student;

        return [
            'course_id' => [
                'required',
                'integer',
                Rule::exists('courses', 'id'),
                Rule::unique('student_courses', 'course_id')
                    ->where('student_id', $studentId),
            ],
            'academic_year' => ['required', 'digits:4'],
            'is_current' => ['sometimes', 'boolean'],
            'school_attendance_status' => ['nullable', 'string', 'max:100000'],
            'failed_discipline_ids' => ['nullable', 'array'],
            'failed_discipline_ids.*' => ['integer', 'distinct', 'exists:disciplines,id'],
            'at_risk_discipline_ids' => ['nullable', 'array'],
            'at_risk_discipline_ids.*' => ['integer', 'distinct', 'exists:disciplines,id'],
        ];
    }

    public static function messages(): array
    {
        return [
            'course_id.required' => 'O curso é obrigatório.',
            'course_id.integer' => 'O curso selecionado é inválido.',
            'course_id.exists' => 'O curso selecionado é inválido.',
            'course_id.unique' => 'Este aluno já possui vínculo com o curso selecionado.',
            'academic_year.required' => 'O ano letivo é obrigatório.',
            'academic_year.digits' => 'O ano letivo deve conter 4 dígitos.',
            'is_current.boolean' => 'O campo de matrícula atual é inválido.',
        ];
    }
}
