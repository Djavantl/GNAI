<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Data\StudentCourses;

use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentCourse;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class UpdateStudentCourseData extends Data
{
    public function __construct(
        public int $courseId,
        public int $academicYear,
        public bool $isCurrent = false,
    ) {}

    public static function rules(): array
    {
        $studentCourse = request()->route('studentCourse');
        $studentId = $studentCourse instanceof StudentCourse
            ? (int) $studentCourse->student_id
            : null;
        $studentCourseId = $studentCourse instanceof StudentCourse
            ? (int) $studentCourse->getKey()
            : null;

        return [
            'course_id' => [
                'required',
                'integer',
                Rule::exists('courses', 'id'),
                Rule::unique('student_courses', 'course_id')
                    ->where('student_id', $studentId)
                    ->ignore($studentCourseId),
            ],
            'academic_year' => ['required', 'digits:4'],
            'is_current' => ['sometimes', 'boolean'],
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
